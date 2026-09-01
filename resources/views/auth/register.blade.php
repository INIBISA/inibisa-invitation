@extends('layouts.auth')
@section('title', 'Daftar')
@section('content')
<h1>Buat akun</h1><p class="muted">Akun baru akan diperiksa admin sebelum dapat membuat undangan.</p>
<form method="POST" action="{{ route('register') }}">@csrf
    <div class="field"><label for="name">Nama</label><input class="input" id="name" name="name" value="{{ old('name') }}" required>@error('name')<span class="error-text">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="email">Email</label><input class="input" id="email" type="email" name="email" value="{{ old('email') }}" required>@error('email')<span class="error-text">{{ $message }}</span>@enderror</div>
    <div class="field"><label for="password">Password</label><input class="input" id="password" type="password" name="password" required><span class="muted">Minimal 8 karakter, mengandung huruf dan angka.</span></div>
    <div class="field"><label for="password_confirmation">Ulangi password</label><input class="input" id="password_confirmation" type="password" name="password_confirmation" required>@error('password')<span class="error-text">{{ $message }}</span>@enderror</div>
    <button class="button gold" type="submit">Daftar</button>
</form><p class="muted">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
