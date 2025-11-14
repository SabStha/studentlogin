@extends('layouts.app')

@section('title', 'ログイン - AIWA FARM')

@section('content')
<div style="max-width: 500px; margin: 100px auto;">
    <h1 style="font-size: 32px; color: #333; margin-bottom: 30px; text-align: left;">
        AIWA FAR<span id="quick-login-m" style="cursor: pointer; color: #0066cc; text-decoration: underline; user-select: none;" title="クリックして自動ログイン">M</span>
    </h1>
    
    <div class="card">
        <form method="POST" action="{{ route('login') }}" id="login-form">
            @csrf
            
            <div class="form-group">
                <input type="text" name="username" id="username" value="{{ old('username', 'miura') }}" required autofocus placeholder="ユーザー名">
                @error('username')
                    <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" required placeholder="パスワード">
                @error('password')
                    <span style="color: #dc3545; font-size: 12px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-pink" style="width: 100%; padding: 12px; font-size: 16px;">ログイン</button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('quick-login-m').addEventListener('click', function() {
    // Auto-fill credentials
    document.getElementById('username').value = 'miura';
    document.getElementById('password').value = 'password';
    
    // Submit the form
    document.getElementById('login-form').submit();
});
</script>
@endsection

