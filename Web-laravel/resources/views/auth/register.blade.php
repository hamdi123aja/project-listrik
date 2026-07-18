@extends('layouts.app')
@section('title', 'Register - Monitoring Listrik')
@section('body')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="brand">
            <h1>Buat Akun</h1>
            <div class="muted">Registrasi untuk akses dashboard monitoring</div>
        </div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="field">
                <label>Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label>Password (min 8 karakter)</label>
                <input type="password" name="password" required>
            </div>
            <div class="field">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button class="btn" type="submit" style="width:100%">Register</button>
        </form>
        <p class="muted" style="margin-top:14px">Sudah punya akun? <a href="{{ route('login') }}" style="color:#7f0f0f;font-weight:700">Login</a></p>
    </div>
</div>
@endsection
