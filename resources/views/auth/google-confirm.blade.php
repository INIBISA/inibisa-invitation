@extends('layouts.auth')
@section('title', 'Konfirmasi Akun Google')
@section('auth-page', 'auth-google-confirm')
@section('content')
    <div class="auth-heading">
        <span class="auth-kicker">Satu langkah lagi</span>
        <h2>Konfirmasi Akun</h2>
        <p>Periksa data Google Anda, lalu setujui dokumen legal untuk menyelesaikan pendaftaran.</p>
    </div>
    @if ($errors->any())
        <div class="auth-error-summary"><svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9" />
                <path d="M12 8v5M12 17h.01" />
            </svg><span>{{ $errors->first() }}</span></div>
    @endif
    <div class="google-confirm-card">
        @if(!empty($pending['avatar']))
            <img src="{{ $pending['avatar'] }}" alt="Foto profil Google" loading="lazy">
        @endif
        <div><strong>{{ $pending['name'] }}</strong><small>{{ $pending['email'] }}</small></div>
    </div>
    <form class="premium-form" method="POST" action="{{ route('google.confirm.store') }}">@csrf
        <label class="terms-consent"><input type="checkbox" name="terms" value="1" @checked(old('terms')) required><span>Saya menyetujui <a href="{{ route('terms') }}" target="_blank" rel="noopener">Syarat dan Ketentuan</a> serta <a href="{{ route('privacy') }}" target="_blank" rel="noopener">Kebijakan Privasi</a>.</span></label>
        @error('terms')
            <span class="field-error">{{ $message }}</span>
        @enderror
        <button class="primary-button full-button" type="submit">Buat Akun dengan Google <svg viewBox="0 0 24 24">
                <path d="M5 12h14M14 7l5 5-5 5" />
            </svg></button>
    </form>
    <p class="auth-switch">Batalkan dan <a href="{{ route('register') }}">kembali ke pendaftaran</a></p>
@endsection
