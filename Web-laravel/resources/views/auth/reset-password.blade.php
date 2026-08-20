@extends('layouts.app')
@section('title', 'Reset Password - Monitoring Listrik')
@section('body')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="brand">
            <h1>Reset Password</h1>
            <div class="muted">Masukkan password baru Anda</div>
        </div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required readonly style="background: var(--bg-3); cursor: not-allowed;">
            </div>
            
            <div class="field">
                <label>Password Baru</label>
                <input type="password" name="password" required autofocus placeholder="Minimal 8 karakter">
            </div>

            <div class="field">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi password baru">
            </div>

            <button class="btn" type="submit" style="width:100%">Reset Password</button>
        </form>
        <p class="muted" style="margin-top:14px">Batal dan kembali ke <a href="{{ route('login') }}" style="color:var(--accent);font-weight:700">Login</a></p>
    </div>
</div>
@endsection
