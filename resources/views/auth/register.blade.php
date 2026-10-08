@extends('layouts.auth')
@section('title', 'Buat Akun')
@section('auth-page', 'auth-register')
@section('content')
    <div class="auth-heading">
        <h2>Buat Akun</h2>
        <p>Mulai buat undangan Anda.</p>
    </div>
    @if ($errors->any())
        <div class="auth-error-summary"><svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 8v5M12 17h.01" />
            </svg><span>{{ $errors->first() }}</span></div>
    @endif
    @include('auth.partials.google-button', ['label' => 'Daftar dengan Google', 'divider' => 'atau'])
    <form class="premium-form" method="POST" action="{{ route('register') }}">@csrf
        <div class="form-field"><label for="name">Nama Lengkap</label>
            <div class="input-shell @error('name') invalid @enderror"><svg viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M4 21a8 8 0 0 1 16 0" />
                </svg><input id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkapmu"
                    autocomplete="name" required autofocus></div>
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-field"><label for="email">Alamat Email</label>
            <div class="input-shell @error('email') invalid @enderror"><svg viewBox="0 0 24 24">
                    <path d="M4 5h16v14H4zM4 7l8 6 8-6" />
                </svg><input id="email" type="email" name="email" value="{{ old('email') }}"
                    placeholder="nama@email.com" autocomplete="email" required></div>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="auth-password-grid">
            <div class="form-field"><label for="password">Kata Sandi</label>
                <div class="input-shell @error('password') invalid @enderror"><svg viewBox="0 0 24 24">
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg><input id="password" type="password" name="password" placeholder="Minimal 6 karakter"
                        autocomplete="new-password" required><button class="password-toggle" type="button"
                        data-password-toggle="password" aria-label="Tampilkan kata sandi"><svg class="eye-open"
                            viewBox="0 0 24 24">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="2.5" />
                        </svg><svg class="eye-closed" viewBox="0 0 24 24">
                            <path
                                d="m3 3 18 18M10.7 6.2c.43-.13.86-.2 1.3-.2 6.5 0 10 6 10 6M6.6 6.6C3.6 8.3 2 12 2 12s3.5 6 10 6c1.65 0 3.1-.39 4.35-1" />
                        </svg></button></div>
            </div>
            <div class="form-field"><label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <div class="input-shell"><svg viewBox="0 0 24 24">
                        <rect x="4" y="10" width="16" height="11" rx="2" />
                        <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                    </svg><input id="password_confirmation" type="password" name="password_confirmation"
                        placeholder="Ulangi kata sandi" autocomplete="new-password" required><button class="password-toggle"
                        type="button" data-password-toggle="password_confirmation" aria-label="Tampilkan kata sandi"><svg
                            class="eye-open" viewBox="0 0 24 24">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="2.5" />
                        </svg><svg class="eye-closed" viewBox="0 0 24 24">
                            <path
                                d="m3 3 18 18M10.7 6.2c.43-.13.86-.2 1.3-.2 6.5 0 10 6 10 6M6.6 6.6C3.6 8.3 2 12 2 12s3.5 6 10 6c1.65 0 3.1-.39 4.35-1" />
                        </svg></button></div>
            </div>
        </div>
        @error('password')
            <span class="field-error password-error">{{ $message }}</span>
        @enderror
        <label class="terms-consent"><input type="checkbox" name="terms" value="1" @checked(old('terms')) required><span>Saya menyetujui <a href="{{ route('terms') }}" target="_blank" rel="noopener">Syarat dan Ketentuan</a> serta <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Kebijakan Privasi</a>.</span></label>
        @error('terms')
            <span class="field-error">{{ $message }}</span>
        @enderror
        <button class="primary-button full-button" type="submit">Buat Akun <svg viewBox="0 0 24 24">
                <path d="M5 12h14M14 7l5 5-5 5" />
            </svg></button>
    </form>
    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
