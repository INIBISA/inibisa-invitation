@extends('layouts.app')
@section('title', 'Musik Pernikahan')
@section('content')
    <section class="dashboard-intro">
        <div><p class="overline">Katalog Audio</p><h1>Musik Pernikahan</h1><p>Kelola musik YouTube yang dapat dipilih untuk undangan pelanggan.</p></div>
        <span class="admin-page-count">{{ number_format($music->total()) }} musik</span>
    </section>

    <section class="music-admin-layout">
        <aside class="panel music-create-card">
            <div class="music-form-heading">
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 18V5l12-2v13M9 9l12-2M9 18a3 3 0 1 1-3-3c1.7 0 3 1.3 3 3ZM21 16a3 3 0 1 1-3-3c1.7 0 3 1.3 3 3Z" /></svg></span>
                <div><h2>Tambah Musik</h2><p>Gunakan tautan video YouTube publik.</p></div>
            </div>
            <form method="POST" action="{{ route('admin.music.store') }}">
                @csrf
                <div class="field"><label for="music-title">Judul musik</label><input class="input" id="music-title" name="title" value="{{ old('title') }}" placeholder="Contoh: Beautiful in White" required></div>
                <div class="field"><label for="music-category">Kategori</label><select class="input" id="music-category" name="category">
                    @foreach (['Romantic', 'Wedding', 'Acoustic', 'Instrumental', 'Islami', 'Piano', 'Modern'] as $category)
                        <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select></div>
                <div class="youtube-search" data-youtube-search data-youtube-search-fill-title data-search-endpoint="{{ route('youtube.music.search') }}">
                    <label for="admin_youtube_music_search">Cari musik di YouTube</label>
                    <div class="youtube-search-controls"><input class="input" id="admin_youtube_music_search" type="search" placeholder="Judul lagu atau penyanyi" maxlength="100" data-youtube-search-query><button class="button secondary" type="button" data-youtube-search-submit>Cari</button></div>
                    <p data-youtube-search-status role="status" aria-live="polite">Pilih hasil untuk mengisi judul dan tautan musik.</p>
                    <div class="youtube-search-results" data-youtube-search-results hidden></div>
                </div>
                <div class="field"><label for="music-url">Tautan YouTube</label><input class="input" id="music-url" name="youtube_url" type="url" placeholder="https://www.youtube.com/watch?v=..." value="{{ old('youtube_url') }}" required><small>Gambar mini dibuat otomatis dari video.</small></div>
                <label class="music-active-check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))><span><strong>Aktifkan musik</strong><small>Tampilkan pada pilihan musik pelanggan.</small></span></label>
                <button class="button gold music-submit" type="submit">Tambah ke Katalog</button>
            </form>
        </aside>

        <section class="panel music-library-panel">
            <div class="panel-header management-header">
                <div><p class="overline">Koleksi</p><h2>Daftar Musik</h2><p>{{ $music->total() }} pilihan tersedia</p></div>
                <form method="GET" class="music-search-form"><input class="input" type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul musik"><button class="button secondary" type="submit">Cari</button>@if(request('search'))<a class="button secondary" href="{{ route('admin.music.index') }}">Atur Ulang</a>@endif</form>
            </div>
            <div class="music-admin-list">
                @forelse($music as $song)
                    <article class="music-admin-card">
                        <button class="music-cover-button" type="button" data-youtube-preview="{{ $song->youtube_video_id }}" aria-label="Pratinjau {{ $song->title }}">
                            <img src="{{ $song->thumbnail }}" alt="Gambar mini {{ $song->title }}" loading="lazy">
                            <span>▶</span>
                        </button>
                        <div class="music-admin-detail">
                            <div class="music-admin-meta"><span class="badge {{ $song->is_active ? 'active' : 'inactive' }}">{{ $song->is_active ? 'Aktif' : 'Nonaktif' }}</span><span>{{ $song->category }}</span></div>
                            <h3>{{ $song->title }}</h3>
                            <details>
                                <summary>Ubah musik</summary>
                                <form method="POST" action="{{ route('admin.music.update', $song) }}">
                                    @csrf @method('PUT')
                                    <div class="form-grid">
                                        <div class="field"><label>Judul</label><input class="input" name="title" value="{{ $song->title }}" required></div>
                                        <div class="field"><label>Kategori</label><select class="input" name="category">@foreach (['Romantic', 'Wedding', 'Acoustic', 'Instrumental', 'Islami', 'Piano', 'Modern'] as $category)<option value="{{ $category }}" @selected($song->category === $category)>{{ $category }}</option>@endforeach</select></div>
                                        <div class="field full"><label>Tautan YouTube</label><input class="input" name="youtube_url" type="url" value="{{ $song->youtube_url }}" required></div>
                                    </div>
                                    <label class="check"><input type="checkbox" name="is_active" value="1" @checked($song->is_active)> Aktif</label>
                                    <div class="actions"><button class="button success small" type="submit">Simpan Perubahan</button></div>
                                </form>
                            </details>
                        </div>
                        <div class="music-admin-actions">
                            <button class="button secondary small" type="button" data-youtube-preview="{{ $song->youtube_video_id }}">Pratinjau</button>
                            <form method="POST" action="{{ route('admin.music.destroy', $song) }}" data-confirm="Hapus pilihan musik ini?">@csrf @method('DELETE')<button class="button danger small" type="submit">Hapus</button></form>
                        </div>
                    </article>
                @empty
                    <div class="music-empty-state"><span>♫</span><h3>{{ request('search') ? 'Musik tidak ditemukan' : 'Belum ada musik' }}</h3><p>{{ request('search') ? 'Coba gunakan kata kunci lain.' : 'Tambahkan link YouTube melalui form untuk membangun katalog.' }}</p></div>
                @endforelse
            </div>
            <div class="admin-pagination">{{ $music->links() }}</div>
        </section>
    </section>

    <dialog class="app-dialog video-dialog" data-video-dialog><button class="icon-button dialog-close" type="button" data-dialog-close aria-label="Tutup">×</button><div data-video-frame></div></dialog>
@endsection
