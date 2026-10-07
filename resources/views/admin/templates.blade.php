@extends('layouts.app')
@section('title', 'Templat')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Katalog Produk</p>
            <h1>Templat</h1>
            <p>Atur harga, status, thumbnail, dan demo templat.</p>
        </div>
    </section>
    <div class="template-grid">
        @forelse($templates as $template)
            <article class="template-card">
                <div class="template-card-image"><img src="{{ asset($template->thumbnail) }}" alt="{{ $template->name }}"
                        loading="lazy"><span class="template-category">{{ $template->category }}</span></div>
                <div class="template-card-body">
                    <div class="actions">
                        <h2>{{ $template->name }}</h2><span
                            class="status-badge {{ $template->is_active ? 'active' : 'inactive' }}"><i></i>{{ $template->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <p>{{ $template->invitations_count }} undangan · Rp {{ number_format($template->price, 0, ',', '.') }}
                    </p>
                    <div class="actions">
                        @if ($template->demo)
                            <a class="button secondary small" href="{{ route('templates.show', $template) }}"
                                target="_blank" rel="noopener">Lihat Demo</a><a class="button secondary small"
                            href="{{ route('admin.demos.edit', $template->demo) }}">Ubah Demo</a>@else<span
                                class="muted">Demo belum tersedia</span>
                        @endif
                    </div>
                    <details>
                        <summary>Pengaturan Templat</summary>
                        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data">@csrf
                            @method('PATCH')<div class="field"><label>Nama</label><input class="input" name="name"
                                    value="{{ $template->name }}" required></div>
                            <div class="field"><label>Kategori</label><input class="input" name="category"
                                    value="{{ $template->category }}" required></div>
                            <div class="field"><label>Harga (Rp)</label><input class="input" type="number" min="1000"
                                    name="price" value="{{ $template->price }}" required></div>
                            <div class="field"><label for="thumbnail-{{ $template->id }}">Thumbnail Paket</label><input class="input" id="thumbnail-{{ $template->id }}" type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" data-max-mb="5"><small>JPG, PNG, atau WebP. Maksimal 5 MB. Rasio disarankan 16:10.</small></div><label
                                class="check"><input type="checkbox" name="is_active" value="1"
                                    @checked($template->is_active)> Aktif</label><button class="button gold small"
                                type="submit">Simpan</button>
                        </form>
                    </details>
                </div>
        </article>@empty<div class="premium-empty">
                <h3>Belum ada templat</h3>
            </div>
        @endforelse
    </div>
@endsection
