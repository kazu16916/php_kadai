<?php
// app/Http/Controllers/ThemeController.php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Category;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ThemeController extends Controller
{
    public function index()
    {
        // 最初はシンプルに、リレーションなしで取得
        $themes = Theme::orderBy('created_at', 'desc')->get();
        return view('themes.index', compact('themes'));
    }

    public function create()
    {
        // Authファサードを使用
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // カテゴリ機能を一時的に無効化
        $categories = collect([]);
        return view('themes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // 認証チェック（Authファサード使用）
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // バリデーションを最小限に
        $request->validate([
            'title' => 'required|string',
            'options' => 'required|array|min:2',
        ]);

        try {
            // デバッグ用の出力
            Log::info('User ID: ' . Auth::id());
            Log::info('Request data: ', $request->all());

            // ユーザーIDの取得
            $userId = Auth::id();
            if (!$userId) {
                // テスト用に最初のユーザーを使用
                $firstUser = User::first();
                $userId = $firstUser ? $firstUser->id : 1;
                Log::warning('認証ユーザーが見つからない。ユーザーID ' . $userId . ' を使用。');
            }

            $theme = Theme::create([
                'title' => $request->title,
                'description' => $request->description ?? '',
                'creator_id' => $userId,
                'category_id' => null, // 一時的にnull
            ]);

            // 選択肢を保存
            foreach ($request->options as $optionName) {
                if (!empty(trim($optionName))) {
                    Option::create([
                        'theme_id' => $theme->id,
                        'name' => trim($optionName),
                    ]);
                }
            }

            return redirect()->route('themes.index')
                            ->with('success', 'テーマが作成されました。');
                            
        } catch (\Exception $e) {
            Log::error('テーマ作成エラー: ' . $e->getMessage());
            return back()->withInput()->with('error', 'エラーが発生しました: ' . $e->getMessage());
        }
    }
}