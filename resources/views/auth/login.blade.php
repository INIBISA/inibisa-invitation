@extends('layouts.auth')
@section('title', 'Masuk')
@section('content')
    <h1>
        Selamat datang</h1>
    <p class="muted">Masuk untuk mengelola undangan Anda.</p>
    <form method="POST" action="{{ route('login') }}">@csrf
        <div class="field"><label for="email">Email</label><input class="input" id="email" type="email" name="email"
                value="{{ old('email') }}" autocomplete="email" required autofocus>
            @error('email')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>
        <div class="field"><label for="password">Password</label><input class="input" id="password" type="password"
                name="password" autocomplete="current-password" required></div>
        <div class="field"><label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        </div>
        <button class="button gold" type="submit">Masuk</button>
    </form>
    <p class="muted">Belum punya akun? <a href="{{ route('register') }}">Daftar customer</a></p>
@endsection
