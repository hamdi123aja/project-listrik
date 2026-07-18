
@extends('layouts.app')
@section('title', 'Login - Monitoring Listrik')
@section('body')
<div class="auth-wrap">
    <div class="auth-card">
        <div class="brand">
            <h1>Monitoring Konsumsi Listrik</h1>
            <div class="muted">Berbasis IoT</div>
        </div>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="field">

            </div>
            <button class="btn" type="submit" style="width:100%">Login</button>
        </form>
        <p class="muted" style="margin-top:14px">Belum punya akun? <a href="{{ route('register') }}" style="color:#7f0f0f;font-weight:700">Register</a></p>
    </div>
</div>
@endsection
