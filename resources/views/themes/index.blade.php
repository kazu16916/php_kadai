@extends('layouts.app')

@section('title', '投票テーマ一覧')

@section('content')
<div class="card">
    <h1 style="color: #4a90e2; margin-bottom: 2rem;">投票テーマ一覧</h1>
    
    @auth
        <a href="{{ route('themes.create') }}" class="btn btn-success">新しいテーマを作成する</a>
    @endauth

    @if($themes->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>概要</th>
                    <th>作成者</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($themes as $theme)
                    <tr>
                        <td>
                            <strong>{{ $theme->title }}</strong>
                            @if($theme->category)
                                <br><small style="color: #666;">カテゴリ: {{ $theme->category->name }}</small>
                            @endif
                        </td>
                        <td>
                            {{ Str::limit($theme->description, 50) }}
                        </td>
                        <td>{{ $theme->creator->username }}</td>
                        <td>
                            <a href="{{ route('votes.show', $theme) }}" class="btn">投票</a>
                            <a href="{{ route('results.show', $theme) }}" class="btn">結果</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #666; margin-top: 2rem;">
            まだテーマがありません。最初のテーマを作成してみませんか？
        </p>
    @endif
</div>
@endsection