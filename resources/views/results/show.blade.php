@extends('layouts.app')

@section('title', $theme->title . ' - 結果')

@section('content')
<div class="card">
    <h1 style="color: #4a90e2;">ランキング結果: {{ $theme->title }}</h1>
    
    <p><strong>総投票数:</strong> {{ $totalVotes }}</p>
    
    @if($totalVotes > 0)
        @foreach($results as $result)
            @php
                $percentage = $totalVotes > 0 ? ($result['votes'] / $totalVotes) * 100 : 0;
                $displayPercentage = round($percentage, 1);
            @endphp
            
            <div class="result-item">
                <div>
                    <strong>{{ $result['name'] }}</strong>
                </div>
                <div style="flex: 1; margin: 0 1rem;">
                    <div class="result-bar">
                        <div class="result-bar-fill" data-width="{{ $percentage }}">
                            {{ $result['votes'] }}票
                        </div>
                    </div>
                </div>
                <div>
                    {{ $displayPercentage }}%
                </div>
            </div>
        @endforeach
    @else
        <p style="text-align: center; color: #666; margin: 2rem 0;">
            まだ投票がありません。
        </p>
    @endif
    
    <div style="margin-top: 2rem;">
        <a href="{{ route('themes.index') }}" class="btn">ほかのテーマを見る</a>
        @if(!auth()->check() || !auth()->user()->votes()->where('theme_id', $theme->id)->exists())
            <a href="{{ route('votes.show', $theme) }}" class="btn btn-success">投票する</a>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.result-bar-fill').forEach(function(element) {
        const width = element.getAttribute('data-width');
        element.style.width = width + '%';
    });
});
</script>
@endsection