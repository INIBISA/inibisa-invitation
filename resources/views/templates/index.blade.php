@extends('layouts.app')
@section('title', 'Pilih Template')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Katalog Desain</p>
            <h1>Pilih Template</h1>
            <p>Bandingkan tampilan demo dan harga. Pilih template untuk melihat ringkasan sebelum membayar.</p>
        </div>
    </section>
    <section class="customer-guide" aria-label="Panduan memilih template">
        <h2>Sebelum memilih</h2>
        <p>Demo menunjukkan contoh tampilan; nama, acara, dan foto Anda diisi setelah pembayaran berhasil. Tombol “Pilih Template” membuka checkout, belum langsung menagih pembayaran.</p>
    </section>
    <div class="template-grid">
        @forelse($templates as $template)
            <article class="template-card">
                <div class="template-card-image"><img src="{{ asset($template->thumbnail) }}"
                        alt="Tampilan {{ $template->name }}" loading="lazy"><span
                        class="template-category">{{ $template->category }}</span></div>
                <div class="template-card-body">
                    <h2>{{ $template->name }}</h2>
                    <p>{{ $template->description ?: 'Desain undangan yang dapat disesuaikan dengan cerita Anda.' }}</p><strong>Rp
                        {{ number_format($template->price, 0, ',', '.') }}</strong>
                    <div class="actions template-card-actions">
                        <a class="button secondary" href="{{ route('templates.show', $template) }}" target="_blank"
                            rel="noopener">Lihat Demo</a>
                        <a class="button gold" href="{{ route('payments.create', $template) }}">Lihat Checkout</a>
                    </div>
                </div>
        </article>@empty<div class="premium-empty">
                <h3>Template belum tersedia</h3>
                <p>Silakan kembali beberapa saat lagi.</p>
            </div>
        @endforelse
    </div>
@endsection
