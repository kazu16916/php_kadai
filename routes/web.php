<?php
// routes/web.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

// トップページ（テーマ一覧）
Route::get('/', [ThemeController::class, 'index'])->name('themes.index');

// 認証関連
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// テーマ関連
Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');
Route::get('/themes/create', [ThemeController::class, 'create'])->name('themes.create')->middleware('auth');
Route::post('/themes', [ThemeController::class, 'store'])->name('themes.store')->middleware('auth');

// 投票関連
Route::get('/themes/{theme}/vote', [VoteController::class, 'show'])->name('votes.show');
Route::post('/themes/{theme}/vote', [VoteController::class, 'store'])->name('votes.store')->middleware('auth');

// 結果表示
Route::get('/themes/{theme}/results', [ResultController::class, 'show'])->name('results.show');