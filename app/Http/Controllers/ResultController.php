<?php

namespace App\Http\Controllers;

use App\Models\Theme;

class ResultController extends Controller
{
    public function show(Theme $theme)
    {
        $theme->load(['options.votes', 'creator', 'category']);
        
        // 各選択肢の投票数を計算
        $results = $theme->options->map(function ($option) {
            return [
                'name' => $option->name,
                'votes' => $option->votes->count(),
            ];
        })->sortByDesc('votes');

        $totalVotes = $theme->votes()->count();

        return view('results.show', compact('theme', 'results', 'totalVotes'));
    }
}