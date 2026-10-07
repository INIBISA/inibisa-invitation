@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <section class="dashboard-intro"><div><p class="overline">Pembayaran</p><h1>{{ $template->name }}</h1><p>Selesaikan pembayaran untuk mulai membuat undangan.</p></div></section>
    <div class="checkout-grid">
        <form class="panel form-section" method="POST" action="{{ route('payments.store') }}">
            @csrf
            <input type="hidden" name="template_id" value="{{ $template->id }}">
            <p class="overline">Metode Aktif</p>
            <h2>{{ $activePaymentMethod === 'midtrans' ? 'Midtrans' : 'Transfer Manual' }}</h2>
            <div class="checkout-method-summary"><span>{{ $activePaymentMethod === 'midtrans' ? 'M' : 'TF' }}</span><div><strong>{{ $activePaymentMethod === 'midtrans' ? 'Pembayaran Otomatis' : 'Verifikasi oleh Admin' }}</strong><p>{{ $activePaymentMethod === 'midtrans' ? 'Bayar melalui Midtrans. Akses terbuka otomatis setelah pembayaran berhasil.' : 'Transfer ke rekening tujuan, lalu unggah bukti pembayaran untuk diperiksa admin.' }}</p></div></div>
            @if ($activePaymentMethod === 'manual_transfer')<div class="bank-instructions">{{ $bank }}</div>@endif
            <div class="field"><label for="payment-note">Catatan untuk admin <em>Opsional</em></label><textarea class="input" id="payment-note" name="note" rows="3" placeholder="Tambahkan catatan bila diperlukan">{{ old('note') }}</textarea></div>
            <button class="button gold" type="submit">Lanjutkan Pembayaran</button>
        </form>
        <aside class="panel checkout-summary"><img src="{{ asset($template->thumbnail) }}" alt="{{ $template->name }}"><h2>{{ $template->name }}</h2><div><span>Total</span><strong>Rp {{ number_format($template->price, 0, ',', '.') }}</strong></div></aside>
    </div>
@endsection
