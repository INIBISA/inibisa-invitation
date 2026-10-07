@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')
    <section class="dashboard-intro">
        <div><p class="overline">Konfigurasi</p><h1>Pengaturan</h1><p>Kelola profil, keamanan, dan metode pembayaran.</p></div>
    </section>
    <form class="settings-layout" method="POST" action="{{ route('admin.settings.update') }}">
        @csrf @method('PATCH')
        <div class="settings-main">
            <section class="panel settings-card">
                <div class="panel-header"><div><h2>Profil</h2><p>Informasi akun administrator.</p></div></div>
                <div class="settings-fields"><div class="form-grid">
                    <div class="form-field"><label>Nama</label><input class="input" name="name" value="{{ old('name', auth()->user()->name) }}" required></div>
                    <div class="form-field"><label>Email</label><input class="input" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" required></div>
                </div></div>
            </section>
            <section class="panel settings-card">
                <div class="panel-header"><div><h2>Metode Pembayaran</h2><p>Satu metode aktif untuk seluruh transaksi baru.</p></div></div>
                <div class="settings-fields">
                    <div class="payment-method-settings">
                        <label class="payment-setting-option"><input type="radio" name="active_payment_method" value="manual_transfer" @checked(old('active_payment_method', $activePaymentMethod) === 'manual_transfer')><span><strong>Transfer Manual</strong><small>Pelanggan mengunggah bukti transfer untuk diperiksa admin.</small></span></label>
                        <label class="payment-setting-option {{ $isMidtransReady ? '' : 'is-disabled' }}"><input type="radio" name="active_payment_method" value="midtrans" @checked(old('active_payment_method', $activePaymentMethod) === 'midtrans') @disabled(! $isMidtransReady)><span><strong>Midtrans</strong><small>{{ $isMidtransReady ? 'Pembayaran diproses otomatis melalui Midtrans.' : 'Server key dan client key Midtrans belum dikonfigurasi.' }}</small></span></label>
                    </div>
                    @error('active_payment_method')<small class="field-error">{{ $message }}</small>@enderror
                    <div class="editor-note">Perubahan hanya berlaku untuk transaksi baru. Transaksi lama tetap memakai metode sebelumnya.</div>
                    <div class="form-field"><label>Informasi rekening transfer manual</label><textarea class="input" name="manual_transfer" rows="5" placeholder="Contoh: BCA 1234567890 a.n. Nama Pemilik">{{ old('manual_transfer', $manualTransfer) }}</textarea><small>Wajib diisi saat Transfer Manual aktif.</small>@error('manual_transfer')<small class="field-error">{{ $message }}</small>@enderror</div>
                </div>
            </section>
            <section class="panel settings-card">
                <div class="panel-header"><div><h2>Ubah Kata Sandi</h2><p>Kosongkan bila tidak ingin mengganti kata sandi.</p></div></div>
                <div class="settings-fields">
                    <div class="form-field"><label>Kata sandi saat ini</label><input class="input" name="current_password" type="password"></div>
                    <div class="form-grid"><div class="form-field"><label>Kata sandi baru</label><input class="input" name="password" type="password"></div><div class="form-field"><label>Konfirmasi kata sandi</label><input class="input" name="password_confirmation" type="password"></div></div>
                </div>
            </section>
        </div>
        <aside class="settings-aside"><button class="primary-button full-button" type="submit">Simpan Pengaturan</button></aside>
    </form>
@endsection
