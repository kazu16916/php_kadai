@extends('layouts.app')

@section('title', '新しいテーマの作成')

@section('content')
<div class="card">
    <h1 style="color: #4a90e2; margin-bottom: 2rem;">新しいテーマの作成</h1>

    {{-- フラッシュメッセージ表示（任意） --}}
    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom:1rem;">
            {{ session('error') }}
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif

    {{-- バリデーションエラー表示（任意） --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom:1rem;">
            <ul style="margin:0;padding-left:1.2rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('themes.store') }}" id="theme-form">
        @csrf

        <div class="form-group">
            <label for="title">タイトル:</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="description">概要:</label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="category_id">カテゴリ:</label>
            <select id="category_id" name="category_id">
                <option value="">カテゴリを選択（任意）</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>選択肢の入力:</label>
            <div id="options-container">
                <div class="form-group">
                    <input type="text" name="options[]" placeholder="選択肢1" value="{{ old('options.0') }}" required>
                </div>
                <div class="form-group">
                    <input type="text" name="options[]" placeholder="選択肢2" value="{{ old('options.1') }}" required>
                </div>
                {{-- 旧入力が3つ以上ある場合の復元 --}}
                @foreach((array)old('options', []) as $idx => $opt)
                    @if($idx >= 2)
                        <div class="form-group">
                            <input type="text" name="options[]" placeholder="選択肢{{ $idx + 1 }}" value="{{ $opt }}">
                            <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger" style="margin-left: 10px;">削除</button>
                        </div>
                    @endif
                @endforeach
            </div>
            <button type="button" id="add-option" class="btn" style="background: #28a745;">選択肢を追加</button>
        </div>

        <button type="submit" class="btn" style="width: 100%;">作成する</button>
    </form>
</div>

<script>
// 余計なCSRF上書きはしません（@csrfに任せる）

document.getElementById('add-option').addEventListener('click', function () {
    const container = document.getElementById('options-container');
    // form-group要素の数をカウントして連番表示
    const optionCount = container.querySelectorAll('.form-group').length + 1;

    const newOption = document.createElement('div');
    newOption.className = 'form-group';
    newOption.innerHTML = `
        <input type="text" name="options[]" placeholder="選択肢${optionCount}">
        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger" style="margin-left: 10px;">削除</button>
    `;

    container.appendChild(newOption);
});
</script>
@endsection
