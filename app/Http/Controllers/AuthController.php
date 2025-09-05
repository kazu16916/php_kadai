<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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

        // ユーザー確認
        $user = User::where('username', $request->username)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'username' => ['ユーザーが見つかりません。'],
            ]);
        }

        // パスワードチェック
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['パスワードが一致しません。'],
            ]);
        }

        Log::info('ログイン前状態', [
            'session_id' => session()->getId(),
            'authenticated' => Auth::check(),
        ]);

        // 手動認証（セッション再生成なし）
        Auth::login($user, true); // remember = true

        Log::info('ログイン直後状態', [
            'session_id' => session()->getId(),
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
        ]);

        // セッション再生成をコメントアウト
        // $request->session()->regenerate();

        Log::info('リダイレクト前最終状態', [
            'session_id' => session()->getId(),
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('themes.index')->with('success', 'ログインしました。');
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
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // 手動ログイン（セッション再生成なし）
        Auth::login($user, true);

        return redirect()->route('themes.index')->with('success', 'アカウントが作成されました。');
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'ログアウトしました。');
    }
}