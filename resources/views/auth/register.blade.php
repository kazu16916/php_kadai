{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', '新規ユーザー登録')

@section('content')
<div class="card" style="max-width: 400px; margin: 0 auto;">
    <h1 style="color: #4a90e2; text-align: center; margin-bottom: 2rem;">新規ユーザー登録</h1>
    
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        <div class="form-group">
            <label for="username">ユーザー名:</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" required>
        </div>

        <div class="form-group">
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%;">登録</button>
    </form>
</div>
@endsection