@extends('layouts.app')
@section('title', 'Buat Templat Visual')
@section('content')
    <section class="dashboard-intro">
        <div><p class="overline">Studio Desain</p><h1>Buat Templat Visual</h1><p>Mulai dari section yang siap diisi, lalu susun ornamen dan elemen di kanvas.</p></div>
        <a class="button secondary" href="{{ route('admin.templates.index') }}">Kembali ke Templat</a>
    </section>
    <div class="visual-create-card">
        <div><span class="visual-create-icon">✦</span><h2>Mulai dari kanvas kosong</h2><p>Section undangan dan data pasangan sudah disiapkan. Desain baru disimpan sebagai templat nonaktif sampai Anda siap menerbitkannya.</p></div>
        <form method="POST" action="{{ route('admin.templates.store') }}">@csrf
            <div class="field"><label for="visual-name">Nama Templat</label><input class="input" id="visual-name" name="name" value="{{ old('name') }}" placeholder="Contoh: Garden Romance" maxlength="150" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror</div>
            <div class="form-grid"><div class="field"><label for="visual-category">Kategori</label><input class="input" id="visual-category" name="category" value="{{ old('category', 'Wedding') }}" maxlength="80" required>@error('category')<small class="field-error">{{ $message }}</small>@enderror</div><div class="field"><label for="visual-price">Harga (Rp)</label><input class="input" id="visual-price" type="number" name="price" value="{{ old('price', 150000) }}" min="1000" max="100000000" required>@error('price')<small class="field-error">{{ $message }}</small>@enderror</div></div>
            <button class="button gold" type="submit">Buat &amp; Buka Studio</button>
        </form>
    </div>
@endsection
@push('styles')<link rel="stylesheet" href="{{ asset('css/template-studio.css') }}">@endpush
