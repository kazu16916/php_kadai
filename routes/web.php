<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// 認証関連
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');// GETでもアクセス可能

// トップページ（テーマ一覧）
Route::get('/', [ThemeController::class, 'index'])->name('themes.index');

// テーマ関連
Route::get('/themes', [ThemeController::class, 'index'])->name('themes.list');
Route::get('/themes/create', [ThemeController::class, 'create'])->name('themes.create')->middleware('auth');
Route::post('/themes', [ThemeController::class, 'store'])->name('themes.store')->middleware('auth');

// 投票関連
Route::get('/themes/{theme}/vote', [VoteController::class, 'show'])->name('votes.show');
Route::post('/themes/{theme}/vote', [VoteController::class, 'store'])->name('votes.store')->middleware('auth');
Route::resource('themes', ThemeController::class)->middleware('auth');


// 結果表示
Route::get('/themes/{theme}/results', [ResultController::class, 'show'])->name('results.show');

// 強化デバッグ用ルート
Route::get('/debug/auth', function() {
    $sessionData = DB::table('sessions')
        ->where('id', session()->getId())
        ->first();
    
    return response()->json([
        'authenticated' => Auth::check(),
        'user' => Auth::user(),
        'auth_id' => Auth::id(),
        'session_id' => session()->getId(),
        'session_data' => session()->all(),
        'session_driver' => config('session.driver'),
        'session_table_data' => $sessionData,
        'custom_auth' => session('custom_auth'),
        'manual_user_id' => session('auth.user_id'),
        'config_auth' => config('auth.defaults'),
    ]);
})->name('debug.auth');

// 手動ログインテスト用ルート
Route::get('/debug/manual-login/{username}', function($username) {
    $user = \App\Models\User::where('username', $username)->first();
    if ($user) {
        Auth::login($user);
        session(['manual_login' => true]);
        return response()->json([
            'status' => 'manually logged in',
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
        ]);
    }
    return response()->json(['error' => 'User not found']);
})->name('debug.manual-login');

Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
});