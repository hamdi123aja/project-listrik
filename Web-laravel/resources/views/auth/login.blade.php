
@extends('layouts.app')
@section('title', 'Login - Monitoring Listrik')
@section('body')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="brand">
            <h1>Monitoring Konsumsi Listrik</h1>
            <div class="muted">Berbasis IoT</div>
        </div>
        @if (session('status'))
            <div class="success-alert" style="background: rgba(0,224,135,0.1); border: 1px solid rgba(0,224,135,0.3); border-left: 3px solid var(--good); color: #80ffd5; padding: 12px 14px; border-radius: 2px; margin-bottom: 20px; font-size: 13px;">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>
            <div class="field">
                <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:8px">
                    <label style="margin-bottom:0">Password</label>
                    <a href="{{ route('password.request') }}" style="color:var(--accent);font-size:11px;font-family:'Space Mono',monospace;text-transform:uppercase;letter-spacing:0.08em">Lupa Password?</a>
                </div>
                <input type="password" name="password" required autocomplete="current-password">
            </div>
            <button class="btn" type="submit" style="width:100%">Login</button>
        </form>
        <p class="muted" style="margin-top:14px">Belum punya akun? <a href="{{ route('register') }}" style="color:var(--accent);font-weight:700">Register</a></p>
    </div>
</div>
@endsection
