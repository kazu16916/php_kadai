<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * ログイン画面を表示
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'password' => 'required|string',
        ]);

        // usernameとpasswordで認証
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'username' => ['ユーザー名またはパスワードが間違っています。'],
        ]);
    }

    /**
     * 新規登録画面を表示
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * 新規登録処理
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'username.required' => 'ユーザー名は必須です。',
            'username.unique' => 'このユーザー名は既に使用されています。',
            'password.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは6文字以上で入力してください。',
        ]);

        // ユーザー作成
        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // 自動ログイン
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
        }

        return redirect('/')->with('success', 'アカウントが作成されました。');
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'ログアウトしました。');
    }
}