<?php

namespace App\Http\Controllers;

use App\Models\Theme;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VoteController extends Controller
{
    /**
     * 投票画面を表示
     */
    public function show(Theme $theme)
    {
        $theme->load(['options', 'creator', 'category']);

        // ユーザーが既に投票済みかチェック（常に数値の users.id を使う）
        $hasVoted = false;
        $userId = null;

        if (Auth::check()) {
            // 標準：数値ID
            $userId = Auth::user()?->id;

            // 環境によっては Auth::id() が username を返すため、救済で引き直す
            if (is_null($userId) && Auth::id()) {
                $userId = User::where('username', Auth::id())->value('id');
            }

            if (!is_null($userId)) {
                $hasVoted = Vote::where('user_id', $userId)
                    ->where('theme_id', $theme->id)
                    ->exists();
            }
        }

        return view('votes.show', compact('theme', 'hasVoted'));
    }

    /**
     * 投票を保存
     */
    public function store(Request $request, Theme $theme)
    {
        // 認証チェック
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        // バリデーション
        $request->validate([
            'option_id' => ['required', 'integer', 'exists:options,id'],
        ], [
            'option_id.required' => '選択肢を選択してください。',
            'option_id.exists'   => '選択された選択肢が存在しません。',
        ]);

        // 数値の users.id を取得（ここが超重要）
        $userId = Auth::user()?->id;
        if (is_null($userId) && Auth::id()) {
            $userId = User::where('username', Auth::id())->value('id');
        }
        if (is_null($userId)) {
            return redirect()->route('login')->with('error', 'ログイン状態を確認できませんでした。再度ログインしてください。');
        }

        // 既に投票済みかチェック
        $existingVote = Vote::where('user_id', $userId)
            ->where('theme_id', $theme->id)
            ->first();

        if ($existingVote) {
            return redirect()->route('results.show', $theme)
                ->with('error', 'このテーマには既に投票済みです。');
        }

        // 選択した選択肢がこのテーマのものかチェック
        $option = $theme->options()->find($request->option_id);
        if (!$option) {
            return back()->with('error', '無効な選択肢です。');
        }

        try {
            Vote::create([
                'user_id'   => $userId,               // ★ 数値IDを保存
                'theme_id'  => $theme->id,
                'option_id' => (int) $request->option_id,
                'voted_at'  => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('投票エラー: '.$e->getMessage());
            return back()->with('error', '投票中にエラーが発生しました。');
        }

        return redirect()->route('results.show', $theme)
            ->with('success', '投票が完了しました！');
    }
}
