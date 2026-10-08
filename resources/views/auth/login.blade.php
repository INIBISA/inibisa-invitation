@extends('layouts.auth')
@section('title', 'Masuk')
@section('auth-page', 'auth-login')
@section('content')
<div class="auth-heading"><h2>Selamat Datang Kembali</h2><p>Masuk ke akun Anda.</p></div>
@if($errors->any())<div class="auth-error-summary"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 17h.01"/></svg><span>{{ $errors->first() }}</span></div>@endif
@include('auth.partials.google-button', ['label' => 'Masuk dengan Google', 'divider' => 'atau'])
<form class="premium-form" method="POST" action="{{ route('login') }}">@csrf
    <div class="form-field"><label for="email">Alamat Email</label><div class="input-shell @error('email') invalid @enderror"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 7l8 6 8-6"/></svg><input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" autocomplete="email" required autofocus></div>@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
    <div class="form-field"><div class="label-row"><label for="password">Kata Sandi</label><button class="text-button" type="button" data-auth-help>Lupa Kata Sandi?</button></div><div class="input-shell"><svg viewBox="0 0 24 24"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg><input id="password" type="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required><button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi"><svg class="eye-open" viewBox="0 0 24 24"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg><svg class="eye-closed" viewBox="0 0 24 24"><path d="m3 3 18 18M10.7 6.2c.43-.13.86-.2 1.3-.2 6.5 0 10 6 10 6a18 18 0 0 1-2.1 2.8M6.6 6.6C3.6 8.3 2 12 2 12s3.5 6 10 6c1.65 0 3.1-.39 4.35-1"/></svg></button></div></div>
    <div class="auth-actions"><label class="premium-check"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span></span>Ingat saya</label>
    <button class="primary-button full-button" type="submit">Masuk <svg viewBox="0 0 24 24"><path d="M5 12h14M14 7l5 5-5 5"/></svg></button></div>
</form>
<p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Buat Akun</a></p>
@endsection
