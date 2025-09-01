@extends('layouts.app')

@section('title', $theme->title . ' - 投票')

@section('content')
<div class="card">
    <h1 style="color: #4a90e2;">{{ $theme->title }}</h1>
    
    @if($theme->category)
        <p><strong>カテゴリ:</strong> {{ $theme->category->name }}</p>
    @endif
    
    <p><strong>作成者:</strong> {{ $theme->creator->username }}</p>
    
    @if($theme->description)
        <div style="margin: 1rem 0; padding: 1rem; background-color: #f8f9fa; border-radius: 5px;">
            <strong>概要</strong><br>
            {{ $theme->description }}
        </div>
    @endif
    
    <hr style="margin: 2rem 0;">

    @if($hasVoted)
        <div class="alert alert-success">
            このテーマには既に投票済みです。
        </div>
        <a href="{{ route('results.show', $theme) }}" class="btn">結果を見る</a>
    @elseif(auth()->check())
        <h2>投票</h2>
        <form method="POST" action="{{ route('votes.store', $theme) }}">
            @csrf
            <div class="radio-group">
                @foreach($theme->options as $option)
                    <label>
                        <input type="radio" name="option_id" value="{{ $option->id }}" required>
                        {{ $option->name }}
                    </label>
                @endforeach
            </div>
            <button type="submit" class="btn btn-success">投票</button>
        </form>
    @else
        <div class="alert alert-error">
            投票するにはログインが必要です。
        </div>
        <a href="{{ route('login') }}" class="btn">ログイン</a>
    @endif
    
    <div style="margin-top: 2rem;">
        <a href="{{ route('themes.index') }}" class="btn">テーマ一覧に戻る</a>
        <a href="{{ route('results.show', $theme) }}" class="btn">結果を見る</a>
    </div>
</div>
@endsection