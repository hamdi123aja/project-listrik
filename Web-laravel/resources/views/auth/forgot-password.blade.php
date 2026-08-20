@extends('layouts.app')
@section('title', 'Lupa Password - Monitoring Listrik')
@section('body')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="brand">
            <h1>Lupa Password</h1>
            <div class="muted">Masukkan email terdaftar untuk mensimulasikan link reset</div>
        </div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        @if (session('status'))
            <div class="success-alert" style="background: rgba(0,224,135,0.1); border: 1px solid rgba(0,224,135,0.3); border-left: 3px solid var(--good); color: #80ffd5; padding: 12px 14px; border-radius: 2px; margin-bottom: 20px; font-size: 13px;">
                {{ session('status') }}
                @if (session('reset_link'))
                    <div style="margin-top: 10px; font-weight: bold;">
                        <a href="{{ session('reset_link') }}" style="color: var(--accent-2); text-decoration: underline;">
                            Klik di sini untuk reset password (Simulasi)
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </div>
            <button class="btn" type="submit" style="width:100%">Kirim Link Reset</button>
        </form>
        <p class="muted" style="margin-top:14px">Kembali ke <a href="{{ route('login') }}" style="color:var(--accent);font-weight:700">Login</a></p>
    </div>
</div>
@endsection
