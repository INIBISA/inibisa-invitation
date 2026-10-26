@extends('layouts.studio')
@section('title', 'Studio · '.$template->name)
@section('content')
    <div class="studio" data-studio data-template-id="{{ $template->id }}" data-saved="{{ session('success') ? '1' : '0' }}" data-upload-url="{{ route('admin.templates.canvas.images.store', $template) }}" data-asset-base="{{ asset('') }}">
        <header class="studio-topbar">
            <div class="studio-topbar-start">
                <a class="studio-back" href="{{ route('admin.templates.index') }}" aria-label="Kembali ke daftar templat" title="Kembali ke daftar templat">←</a>
                <span class="studio-topbar-divider" aria-hidden="true"></span>
                <div class="studio-title"><span class="studio-kicker">Studio Desain</span><h1>{{ $template->name }}</h1></div>
                <span class="studio-draft-badge">{{ $template->is_active ? 'Aktif' : 'Belum aktif' }}</span>
            </div>
            <div class="studio-topbar-actions">
                <span class="studio-save-state" data-save-state role="status" aria-live="polite">{{ $template->design_draft_settings ? 'Draft tersimpan' : 'Belum ada draft' }}</span>
                <button class="studio-mobile-nav" type="button" data-panel-toggle="sections" aria-controls="studio-sections" aria-expanded="false">Section</button>
                <button class="studio-mobile-nav" type="button" data-panel-toggle="properties" aria-controls="studio-properties" aria-expanded="false">Properti</button>
                <a class="studio-action studio-action-secondary" href="{{ route('admin.templates.design-preview', $template) }}" target="_blank" rel="noopener" data-preview-link>Pratinjau</a>
                <form method="POST" action="{{ route('admin.templates.design-publication.store', $template) }}" data-publish-form>@csrf
                    <button class="studio-action studio-action-secondary" type="submit" @disabled(!$template->design_draft_settings) title="{{ $template->design_draft_settings ? 'Terbitkan draft tersimpan' : 'Simpan draft terlebih dahulu' }}">Terbitkan</button>
                </form>
                <button class="studio-action studio-action-primary" type="submit" form="studio-form">Simpan Draft <span class="studio-key-hint">⌘S</span></button>
            </div>
        </header>

        @if (session('success'))
            <div class="studio-notice studio-notice-success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="studio-notice studio-notice-error" role="alert"><strong>Desain belum tersimpan.</strong> {{ $errors->first() }}</div>
        @endif

        <form id="studio-form" method="POST" action="{{ route('admin.templates.canvas.update', $template) }}">@csrf @method('PUT')<input type="hidden" name="payload" data-studio-payload></form>

        <div class="studio-shell">
            <aside class="studio-side studio-sections" id="studio-sections" data-panel-content="sections" aria-label="Daftar section">
                <div class="studio-side-header"><div><span class="studio-overline">STRUKTUR</span><h2>Section</h2></div><span class="studio-count">{{ count($sections) }}</span></div>
                <p class="studio-side-intro">Pilih area untuk didesain. Urutkan dengan tombol panah.</p>
                <nav class="studio-section-list" data-section-list aria-label="Section templat"></nav>
                <div class="studio-side-bottom"><strong>Tip singkat</strong><p>Cover dan penutup selalu tampil. Section lain bisa disembunyikan tanpa menghapus desainnya.</p></div>
            </aside>

            <main class="studio-workspace" aria-label="Kanvas desain">
                <div class="studio-workspace-toolbar">
                    <div class="studio-workspace-title"><span class="studio-overline">SEDANG DIEDIT</span><strong data-current-section>Cover</strong><small data-current-meta>Seret teks atau elemen untuk mengatur posisinya</small></div>
                    <div class="studio-workspace-tools"><div class="studio-device-switch" role="group" aria-label="Ukuran kanvas"><button type="button" class="active" data-device="desktop" aria-pressed="true">Desktop</button><button type="button" data-device="mobile" aria-pressed="false">Mobile</button></div><button class="studio-fullscreen-toggle" type="button" data-toggle-fullscreen aria-label="Masuk layar penuh" title="Layar penuh">⛶</button></div>
                </div>
                <div class="studio-stage"><div class="studio-canvas" data-canvas><div class="studio-canvas-content" data-canvas-content></div><div class="studio-canvas-layers" data-canvas-layers></div></div></div>
                <div class="studio-stage-footer"><span><span class="studio-footer-dot"></span>Pratinjau kanvas</span><span>Posisi elemen menggunakan persen agar mengikuti ukuran layar</span></div>
            </main>

            <aside class="studio-side studio-properties" id="studio-properties" data-panel-content="properties" aria-label="Pengaturan desain">
                <div class="studio-side-header"><div><span class="studio-overline">ALAT DESAIN</span><h2>Properti</h2></div></div>
                <div class="studio-add-tools" role="group" aria-label="Tambahkan elemen"><button type="button" data-add="text"><span aria-hidden="true">T</span>Teks</button><button type="button" data-add="shape"><span aria-hidden="true">◇</span>Bentuk</button><button type="button" data-add="image"><span aria-hidden="true">▧</span>Gambar</button></div>
                <input type="file" accept="image/jpeg,image/png,image/webp" data-image-input hidden>
                <div class="studio-inspector-tabs" role="tablist" aria-label="Jenis pengaturan"><button type="button" role="tab" data-inspector-tab="section" aria-selected="true">Section</button><button type="button" role="tab" data-inspector-tab="element" aria-selected="false">Elemen</button><button type="button" role="tab" data-inspector-tab="theme" aria-selected="false">Tema</button></div>

                <div class="studio-inspector-panel" data-inspector-panel="section" role="tabpanel">
                    <div class="studio-control-group"><h3>Tampilan section</h3><label>Warna latar<input type="color" data-section-background></label><label>Tinggi minimum <output data-height-output></output><input type="range" min="360" max="1000" step="10" data-section-height></label></div>
                    <div class="studio-control-group"><h3>Konten bawaan</h3><p class="studio-hint">Seret blok teks di kanvas, atau atur posisinya di sini.</p><div class="studio-property-grid"><label>Posisi X (%)<input type="number" min="0" max="70" step="0.1" data-content-property="content_x"></label><label>Jarak atas (px)<input type="number" min="0" max="300" step="1" data-content-property="content_top"></label><label>Lebar (%)<input type="number" min="30" max="100" step="0.1" data-content-property="content_width"></label></div><label>Perataan teks<select data-content-property="content_align"><option value="left">Kiri</option><option value="center">Tengah</option><option value="right">Kanan</option></select></label></div>
                </div>

                <div class="studio-inspector-panel" data-inspector-panel="element" role="tabpanel" hidden>
                    <div class="studio-empty-selection" data-empty-selection><span aria-hidden="true">◇</span><strong>Pilih elemen di kanvas</strong><p>Atur teks, bentuk, ukuran, warna, dan posisi setelah memilih elemen.</p></div>
                    <div class="studio-control-group" data-element-properties hidden><div class="studio-properties-title"><h3>Elemen terpilih</h3><button type="button" class="studio-text-button studio-danger" data-delete-element>Hapus</button></div><label data-text-control>Teks<textarea rows="3" maxlength="300" data-property="content"></textarea></label><label data-shape-control>Bentuk<select data-property="shape"><option value="circle">Lingkaran</option><option value="rectangle">Persegi</option><option value="line">Garis</option></select></label><div class="studio-property-grid"><label>Warna<input type="color" data-property="color"></label><label>Opasitas<input type="number" min="0" max="1" step="0.05" data-property="opacity"></label><label>Posisi X (%)<input type="number" min="0" max="100" step="0.1" data-property="x"></label><label>Posisi Y (%)<input type="number" min="0" max="100" step="0.1" data-property="y"></label><label>Lebar (%)<input type="number" min="5" max="100" step="0.1" data-property="width"></label><label>Tinggi (%)<input type="number" min="2" max="100" step="0.1" data-property="height"></label><label data-font-control>Ukuran font<input type="number" min="10" max="96" data-property="font_size"></label><label>Rotasi (°)<input type="number" min="-180" max="180" data-property="rotation"></label></div></div>
                </div>

                <div class="studio-inspector-panel" data-inspector-panel="theme" role="tabpanel" hidden>
                    <div class="studio-control-group"><h3>Warna templat</h3><p class="studio-hint">Palet ini dipakai oleh konten bawaan pada seluruh section.</p><div class="studio-property-grid"><label>Warna utama<input type="color" data-design-color="primary_color"></label><label>Warna teks<input type="color" data-design-color="text_color"></label><label>Warna dasar<input type="color" data-design-color="background_color"></label></div></div>
                    <div class="studio-control-group"><h3>Tipografi</h3><label>Font judul<select data-design-font="heading_font"><option value="serif">Serif</option><option value="sans">Sans Serif</option><option value="monospace">Monospace</option></select></label><label>Font isi<select data-design-font="body_font"><option value="sans">Sans Serif</option><option value="serif">Serif</option></select></label></div>
                </div>
                <div class="studio-side-bottom studio-property-help"><strong>Gambar & ornamen</strong><p>Unggah PNG transparan, JPG, atau WebP hingga 5 MB. Perubahan diterapkan setelah Anda menyimpan draft.</p></div>
            </aside>
        </div>
        <button class="studio-mobile-backdrop" type="button" data-panel-close aria-label="Tutup panel" hidden></button>
        <footer class="studio-statusbar"><span data-save-state-mobile role="status" aria-live="polite">{{ $template->design_draft_settings ? 'Draft tersimpan' : 'Belum ada draft' }}</span><span>⌘ / Ctrl + S untuk menyimpan · Esc untuk batal memilih</span></footer>
    </div>
@endsection
@push('styles')<link rel="stylesheet" href="{{ asset('css/template-studio.css') }}?v={{ filemtime(public_path('css/template-studio.css')) }}">@endpush
@push('scripts')
    <script>window.templateStudioData = {{ Js::from(['design' => $design, 'sections' => $sections, 'demo' => $template->demo?->data]) }};</script>
    <script src="{{ asset('js/template-studio.js') }}?v={{ filemtime(public_path('js/template-studio.js')) }}" defer></script>
@endpush
