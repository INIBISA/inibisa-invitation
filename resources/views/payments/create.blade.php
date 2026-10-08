@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <section class="dashboard-intro checkout-intro">
        <div><p class="overline">Pembayaran</p><h1>{{ $template->name }}</h1><p>Selesaikan pembayaran untuk mulai membuat undangan.</p></div>
        <a class="button secondary" href="{{ route('templates.index') }}">Ganti Templat</a>
    </section>
    <div class="checkout-grid">
        <form class="panel form-section checkout-main" method="POST" action="{{ route('payments.store') }}">
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">
            <div class="checkout-method-head">
                <div>
                    <p class="overline">Metode Pembayaran</p>
                    <h2>{{ $activePaymentMethod === 'midtrans' ? 'Midtrans' : 'Transfer Manual' }}</h2>
                </div>
                <span class="status-badge {{ $activePaymentMethod === 'midtrans' ? 'paid' : 'waiting_approval' }}"><i></i>{{ $activePaymentMethod === 'midtrans' ? 'Otomatis' : 'Verifikasi Admin' }}</span>
            </div>
            <p class="checkout-method-desc">{{ $activePaymentMethod === 'midtrans' ? 'Bayar melalui Midtrans. Akses templat terbuka otomatis setelah pembayaran berhasil.' : 'Transfer ke rekening tujuan di bawah, lalu unggah bukti pembayaran untuk diperiksa admin.' }}</p>
            @if ($activePaymentMethod === 'manual_transfer')
                <div class="bank-card">
                    <div class="bank-card-head"><span>Rekening Tujuan</span><button class="button small secondary" type="button" data-copy-text="{{ $bank }}" data-copy-message="Informasi rekening disalin">Salin</button></div>
                    <p class="bank-instructions">{{ $bank }}</p>
                </div>
            @endif
            @error('template_id')<div class="alert error">{{ $message }}</div>@enderror
            <div class="field"><label for="payment-note">Catatan untuk admin <em>Opsional</em></label><textarea class="input" id="payment-note" name="note" rows="3" placeholder="Tambahkan catatan bila diperlukan">{{ old('note') }}</textarea></div>
            <ul class="checkout-trust">
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 3v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6z" /></svg>Transaksi tercatat dan aman</li>
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 12 4 4L19 6" /></svg>{{ $activePaymentMethod === 'midtrans' ? 'Status diperbarui otomatis oleh Midtrans' : 'Bukti pembayaran diperiksa admin' }}</li>
                <li><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></svg>Akses templat setelah pembayaran berhasil</li>
            </ul>
            <button class="button gold checkout-submit" type="submit">Lanjutkan Pembayaran · Rp {{ number_format($template->price, 0, ',', '.') }}</button>
        </form>
        <aside class="panel checkout-summary">
            <img src="{{ asset($template->thumbnail) }}" alt="{{ $template->name }}">
            <div class="checkout-summary-title"><span>Ringkasan Pesanan</span><h2>{{ $template->name }}</h2></div>
            <dl>
                <div><dt>Harga templat</dt><dd>Rp {{ number_format($template->price, 0, ',', '.') }}</dd></div>
                <div><dt>Metode</dt><dd>{{ $activePaymentMethod === 'midtrans' ? 'Midtrans' : 'Transfer Manual' }}</dd></div>
                <div><dt>Total bayar</dt><dd>Rp {{ number_format($template->price, 0, ',', '.') }}</dd></div>
            </dl>
            <small>Metode pembayaran ditentukan oleh admin dan berlaku untuk transaksi ini.</small>
        </aside>
    </div>
@endsection
