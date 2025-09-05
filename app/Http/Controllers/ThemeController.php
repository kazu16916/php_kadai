<?php
// app/Http/Controllers/ThemeController.php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Category;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = Theme::orderBy('created_at', 'desc')->get();
        return view('themes.index', compact('themes'));
    }

    public function create()
    {
        // ★ ここを修正：カテゴリーをDBから取得して渡す
        $categories = Category::orderBy('id')->get(); // or ->orderBy('name')
        return view('themes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // ★ category_id をバリデーション対象に追加（任意選択）
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'options'     => ['required', 'array', 'min:2'],
            'options.*'   => ['nullable', 'string', 'max:255'],
        ]);

        try {
            // ★ 常に数値の users.id を取得
            $authUser = Auth::user();
            $userId = $authUser?->id;
            $loginIdentifier = Auth::id(); // あなたの環境では "psyduck"（= username）

            if (is_null($userId) && $loginIdentifier) {
                $userId = User::where('username', $loginIdentifier)->value('id');
            }
            if (is_null($userId)) {
                return redirect()->route('login')
                    ->with('error', 'ログイン状態を確認できませんでした。再度ログインしてください。');
            }

            // オプション整形（空要素除去）
            $options = [];
            foreach (($validated['options'] ?? []) as $name) {
                $name = trim((string)$name);
                if ($name !== '') $options[] = $name;
            }
            if (count($options) < 2) {
                return back()->withInput()->with('error', '選択肢は最低2つ必要です。');
            }

            // ★ category_id: 空文字のときは null に正規化
            $categoryId = $validated['category_id'] ?? null;
            if ($categoryId === '' || $categoryId === 0) {
                $categoryId = null;
            }

            DB::beginTransaction();

            $theme = new Theme();
            $theme->title       = $validated['title'];
            $theme->description = $validated['description'] ?? '';
            $theme->creator_id  = $userId;
            $theme->category_id = $categoryId; // ★ ここで保存
            $theme->save();

            foreach ($options as $optionName) {
                $opt = new Option();
                $opt->theme_id = $theme->id;
                $opt->name     = $optionName;
                $opt->save();
            }

            DB::commit();

            return redirect()->route('themes.index')
                ->with('success', 'テーマが作成されました。');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('テーマ作成エラー: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withInput()
                ->with('error', 'エラーが発生しました: '.$e->getMessage());
        }
    }
}
