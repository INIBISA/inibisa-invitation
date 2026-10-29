@extends('layouts.app')
@section('title', 'Templat')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Katalog Produk</p>
            <h1>Templat</h1>
            <p>Kelola templat kode dan buat desain baru lewat studio visual.</p>
        </div>
        <a class="button gold" href="{{ route('admin.templates.create') }}">＋ Buat Templat Visual</a>
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
                    <p>{{ $template->description ?: 'Belum ada deskripsi.' }}</p>
                    <p>{{ $template->invitations_count }} undangan · Rp {{ number_format($template->price, 0, ',', '.') }}</p>
                    <div class="actions">
                        @if ($template->isVisual())<a class="button gold small" href="{{ route('admin.templates.canvas.edit', $template) }}">Buka Studio</a>@endif
                        @if ($template->demo)
                            <a class="button secondary small" href="{{ route('templates.show', $template) }}"
                                target="_blank" rel="noopener">Lihat Demo</a><a class="button secondary small"
                            href="{{ route('admin.demos.edit', $template->demo) }}">Ubah Demo</a>@else<span
                                class="muted">Demo belum tersedia</span>
                        @endif
                    </div>
                    @php($hasOldInput = (int) old('template_form_id') === $template->id)
                    <button class="button secondary small" type="button" data-template-settings-open="{{ $template->id }}" aria-haspopup="dialog" aria-controls="template-settings-{{ $template->id }}">Pengaturan Templat</button>
                    <dialog class="app-dialog template-settings-dialog" id="template-settings-{{ $template->id }}" aria-labelledby="template-settings-title-{{ $template->id }}">
                        <div class="template-settings-heading"><div><p class="overline">Pengaturan Templat</p><h2 id="template-settings-title-{{ $template->id }}">{{ $template->name }}</h2><p>Ubah informasi yang tampil pada beranda dan katalog, serta atur desain undangan.</p></div><button class="icon-button" type="button" data-dialog-close aria-label="Tutup pengaturan {{ $template->name }}">×</button></div>
                        @if ($hasOldInput && $errors->any())
                            <div class="alert error" role="alert"><strong>Periksa kembali pengaturan:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                        @endif
                        @php($design = $hasOldInput ? array_replace_recursive($template->draftDesignSettings(), old('design_settings', [])) : $template->draftDesignSettings())
                        <form method="POST" action="{{ route('admin.templates.update', $template) }}" enctype="multipart/form-data" data-design-form>@csrf
                            @method('PATCH')<input type="hidden" name="template_form_id" value="{{ $template->id }}"><div class="field"><label>Nama</label><input class="input" name="name"
                                    value="{{ $hasOldInput ? old('name') : $template->name }}" required></div>
                            <div class="field"><label for="description-{{ $template->id }}">Deskripsi Templat</label><textarea class="input" id="description-{{ $template->id }}" name="description" rows="3" maxlength="500" placeholder="Ceritakan nuansa dan keunikan desain ini">{{ $hasOldInput ? old('description') : $template->description }}</textarea><small>Ditampilkan di beranda dan katalog pelanggan. Maksimal 500 karakter.</small></div>
                            <div class="field"><label>Kategori</label><input class="input" name="category"
                                    value="{{ $hasOldInput ? old('category') : $template->category }}" required></div>
                            <div class="field"><label>Harga (Rp)</label><input class="input" type="number" min="1000"
                                    name="price" value="{{ $hasOldInput ? old('price') : $template->price }}" required></div>
                            <div class="field"><label for="thumbnail-{{ $template->id }}">Thumbnail Paket</label><input class="input" id="thumbnail-{{ $template->id }}" type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" data-max-mb="5"><small>JPG, PNG, atau WebP. Maksimal 5 MB. Rasio 4:3 disarankan agar sama dengan thumbnail lainnya.</small></div>
                            @unless ($template->isVisual())
                            <fieldset class="design-settings">
                                <legend>Tampilan Undangan</legend>
                                <div class="design-color-grid">
                                    <div class="field"><label for="primary-{{ $template->id }}">Warna Utama</label><input id="primary-{{ $template->id }}" type="color" name="design_settings[primary_color]" value="{{ $design['primary_color'] }}" data-design-variable="--template-primary"></div>
                                    <div class="field"><label for="text-{{ $template->id }}">Warna Teks</label><input id="text-{{ $template->id }}" type="color" name="design_settings[text_color]" value="{{ $design['text_color'] }}" data-design-variable="--template-text"></div>
                                    <div class="field"><label for="background-{{ $template->id }}">Latar</label><input id="background-{{ $template->id }}" type="color" name="design_settings[background_color]" value="{{ $design['background_color'] }}" data-design-variable="--template-background"></div>
                                </div>
                                <div class="form-grid">
                                    <div class="field"><label for="heading-font-{{ $template->id }}">Font Judul</label><select class="input" id="heading-font-{{ $template->id }}" name="design_settings[heading_font]" data-design-variable="--template-heading-font"><option value="serif" @selected($design['heading_font'] === 'serif')>Serif</option><option value="sans" @selected($design['heading_font'] === 'sans')>Sans Serif</option><option value="monospace" @selected($design['heading_font'] === 'monospace')>Monospace</option></select></div>
                                    <div class="field"><label for="body-font-{{ $template->id }}">Font Isi</label><select class="input" id="body-font-{{ $template->id }}" name="design_settings[body_font]" data-design-variable="--template-body-font"><option value="sans" @selected($design['body_font'] === 'sans')>Sans Serif</option><option value="serif" @selected($design['body_font'] === 'serif')>Serif</option></select></div>
                                    <div class="field"><label for="radius-{{ $template->id }}">Sudut Kartu: <output>{{ $design['card_radius'] }}px</output></label><input class="input" id="radius-{{ $template->id }}" type="range" min="0" max="32" name="design_settings[card_radius]" value="{{ $design['card_radius'] }}" data-design-variable="--template-card-radius" data-design-unit="px"></div>
                                    <div class="field"><label for="overlay-{{ $template->id }}">Overlay Cover: <output>{{ $design['hero_overlay'] }}%</output></label><input class="input" id="overlay-{{ $template->id }}" type="range" min="0" max="80" name="design_settings[hero_overlay]" value="{{ $design['hero_overlay'] }}" data-design-variable="--template-overlay" data-design-unit="%"></div>
                                </div>
                                <div class="field"><label>Section yang Ditampilkan</label><div class="check-grid design-section-grid">
                                    @foreach (['quote' => 'Quote', 'countdown' => 'Countdown', 'events' => 'Acara', 'story' => 'Cerita', 'gallery' => 'Galeri', 'rsvp' => 'RSVP', 'gift' => 'Hadiah', 'wishes' => 'Ucapan'] as $section => $label)
                                        <label class="check"><input type="hidden" name="design_settings[sections][{{ $section }}]" value="0"><input type="checkbox" name="design_settings[sections][{{ $section }}]" value="1" @checked($design['sections'][$section])> {{ $label }}</label>
                                    @endforeach
                                </div><small>Pengaturan undangan pelanggan tetap berlaku. Section tampil hanya jika diaktifkan di kedua tempat.</small></div>
                                <div class="field"><label>Urutan Blok</label><div class="design-order" data-design-order>
                                    @foreach ($design['section_order'] as $section)
                                        <div data-order-item><input type="hidden" name="design_settings[section_order][]" value="{{ $section }}"><span>{{ $template->orderableSections()[$section] }}</span><div><button class="button secondary small" type="button" data-order-up aria-label="Naikkan {{ $template->orderableSections()[$section] }}">Naik</button><button class="button secondary small" type="button" data-order-down aria-label="Turunkan {{ $template->orderableSections()[$section] }}">Turun</button></div></div>
                                    @endforeach
                                </div><small>Cover, pembuka, dan penutup tetap pada posisinya.</small></div>
                                @if ($template->demo)
                                    <button class="button secondary small" type="button" data-design-preview-toggle>Tampilkan Preview Draft Tersimpan</button>
                                    <div class="design-preview-panel" data-design-preview-panel hidden>
                                        <div class="design-preview-toolbar" aria-label="Ukuran preview">
                                            <button class="button secondary small active" type="button" data-preview-size="desktop">Desktop</button>
                                            <button class="button secondary small" type="button" data-preview-size="mobile">Mobile</button>
                                        </div>
                                        <div class="design-preview-frame" data-preview-frame="desktop"><iframe class="design-preview" title="Preview draft {{ $template->name }}" data-src="{{ route('admin.templates.design-preview', $template) }}"></iframe></div>
                                    </div>
                                @endif
                            </fieldset>
                            @endunless
                            <label
                                 class="check"><input type="checkbox" name="is_active" value="1"
                                    @checked($hasOldInput ? old('is_active') : $template->is_active)> Aktif</label><button class="button gold small"
                                type="submit">Simpan Pengaturan & Draft</button>
                        </form>
                        @if ($template->design_draft_settings)
                            <div class="design-draft-status"><span class="status-badge inactive"><i></i>Draft belum diterbitkan</span><small>Pengunjung masih melihat desain publik.</small></div>
                            <div class="design-draft-actions">
                                <form method="POST" action="{{ route('admin.templates.design-publication.store', $template) }}" data-confirm="Terbitkan draft desain? Semua undangan yang memakai templat ini akan langsung berubah.">@csrf<button class="button gold small" type="submit">Terbitkan Draft</button></form>
                                <form method="POST" action="{{ route('admin.templates.design-draft.destroy', $template) }}" data-confirm="Buang draft desain? Perubahan yang belum diterbitkan akan hilang.">@csrf @method('DELETE')<button class="button danger small" type="submit">Buang Draft</button></form>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('admin.templates.design-reset.store', $template) }}" class="design-reset-form" data-confirm="Buat draft dari desain bawaan? Desain publik belum berubah sampai draft diterbitkan.">@csrf<button class="button secondary small" type="submit">Reset ke Bawaan</button></form>
                        @if ($template->designRevisions->isNotEmpty())
                            <div class="design-history"><strong>Riwayat Desain</strong>
                                @foreach ($template->designRevisions->take(5) as $revision)
                                    @php($revisionDesign = $template->normalizedDesignSettings($revision->settings))
                                    <div class="design-history-item"><div><span>{{ $revision->created_at->translatedFormat('d M Y H:i') }} · {{ $revision->creator?->name ?? 'Admin terhapus' }}</span><div class="design-revision-summary"><span class="design-swatches" aria-label="Warna utama, teks, dan latar"><i style="background: {{ $revisionDesign['primary_color'] }}"></i><i style="background: {{ $revisionDesign['text_color'] }}"></i><i style="background: {{ $revisionDesign['background_color'] }}"></i></span><small>{{ count(array_filter($revisionDesign['sections'])) }} section · {{ implode(' → ', array_map(fn ($section) => $template->orderableSections()[$section], $revisionDesign['section_order'])) }}</small></div></div><form method="POST" action="{{ route('admin.templates.design-revisions.rollback', [$template, $revision]) }}" data-confirm="Kembalikan desain publik ke versi {{ $revision->created_at->translatedFormat('d M Y H:i') }}?">@csrf<button class="button secondary small" type="submit">Rollback</button></form></div>
                                @endforeach
                            </div>
                        @endif
                    </dialog>
                </div>
        </article>@empty<div class="premium-empty">
                <h3>Belum ada templat</h3>
            </div>
        @endforelse
    </div>
@endsection

@push('styles')
    <style>
        .design-settings { margin: 18px 0; padding: 16px; border: 1px solid var(--border); border-radius: 12px; }
        .design-settings legend { padding: 0 7px; font: 400 1.05rem var(--serif); }
        .design-color-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .design-color-grid input { width: 100%; height: 44px; padding: 3px; border: 1px solid var(--border); border-radius: 9px; background: #fff; }
        .design-settings input[type="range"] { padding: 0; }
        .design-section-grid { grid-template-columns: repeat(4, 1fr); }
        .design-order { display: grid; gap: 7px; }
        .design-order > div { padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px; border: 1px solid var(--border); border-radius: 9px; background: #fff; }
        .design-order > div > div { display: flex; gap: 5px; }
        .design-preview-panel { margin-top: 12px; }
        .design-preview-toolbar { margin-bottom: 8px; display: flex; justify-content: center; gap: 6px; }
        .design-preview-toolbar .active { color: #fff; border-color: var(--primary); background: var(--primary); }
        .design-preview-frame { width: 100%; margin: auto; transition: width .2s; }
        .design-preview-frame[data-preview-frame="mobile"] { width: min(390px, 100%); }
        .design-preview { display: block; width: 100%; height: 640px; border: 1px solid var(--border); border-radius: 10px; background: #fff; }
        .design-draft-status { margin-top: 13px; display: flex; align-items: center; gap: 10px; }
        .design-draft-actions { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 7px; }
        .design-reset-form { margin-top: 8px; }
        .design-history { margin-top: 16px; display: grid; gap: 8px; }
        .design-history-item { padding: 9px 0; display: flex; align-items: center; justify-content: space-between; gap: 10px; border-bottom: 1px solid var(--border); font-size: .7rem; }
        .design-revision-summary { margin-top: 5px; display: flex; align-items: center; gap: 8px; color: var(--muted); }
        .design-swatches { display: flex; }
        .design-swatches i { width: 17px; height: 17px; border: 1px solid rgba(0,0,0,.16); border-radius: 50%; }
        .design-swatches i + i { margin-left: -4px; }
        .template-card-body { min-width: 0; }
        .template-card-body .actions { flex-wrap: wrap; }
        .template-settings-dialog { width: min(760px, calc(100vw - 32px)); max-width: none; max-height: min(90vh, 920px); overflow-y: auto; padding: clamp(20px, 4vw, 36px); }
        .template-settings-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; margin-bottom: 20px; }
        .template-settings-heading .overline { margin-bottom: 5px; }
        .template-settings-heading p:last-child { margin: 0; }
        .template-settings-heading .icon-button { flex: none; }
        .template-settings-dialog [data-design-form] { display: grid; gap: 16px; }
        .template-settings-dialog .design-settings { margin: 0; min-width: 0; }
        .template-settings-dialog .design-history-item, .template-settings-dialog .design-order > div { flex-wrap: wrap; }
        .template-settings-dialog .design-order > div > div { margin-left: auto; }
        .template-settings-dialog .alert ul { margin-bottom: 0; padding-left: 20px; }
        @media (max-width: 640px) { .template-settings-dialog { max-height: calc(100dvh - 24px); } .design-color-grid, .design-section-grid, .template-settings-dialog .form-grid { grid-template-columns: 1fr 1fr; } .design-preview { height: 520px; } }
        @media (max-width: 480px) { .template-settings-dialog .design-color-grid, .template-settings-dialog .form-grid { grid-template-columns: 1fr; } }
    </style>
@endpush

@push('scripts')
    <script>
        document.querySelectorAll('[data-template-settings-open]').forEach((button) => {
            button.addEventListener('click', () => document.getElementById(`template-settings-${button.dataset.templateSettingsOpen}`).showModal());
        });
        document.querySelectorAll('.template-settings-dialog').forEach((dialog) => {
            dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
        });
        @if (old('template_form_id'))
            document.getElementById('template-settings-{{ (int) old('template_form_id') }}')?.showModal();
        @endif
        document.querySelectorAll('[data-design-form]').forEach((form) => {
            const preview = form.querySelector('.design-preview');
            const previewPanel = form.querySelector('[data-design-preview-panel]');
            const previewFrame = form.querySelector('[data-preview-frame]');

            form.querySelector('[data-design-preview-toggle]')?.addEventListener('click', (event) => {
                if (!preview.src) preview.src = preview.dataset.src;
                previewPanel.hidden = !previewPanel.hidden;
                event.currentTarget.textContent = previewPanel.hidden ? 'Tampilkan Preview Draft Tersimpan' : 'Sembunyikan Preview Draft Tersimpan';
            });

            form.querySelector('.design-preview-toolbar')?.addEventListener('click', (event) => {
                const button = event.target.closest('[data-preview-size]');
                if (!button) return;
                previewFrame.dataset.previewFrame = button.dataset.previewSize;
                button.parentElement.querySelectorAll('[data-preview-size]').forEach((item) => item.classList.toggle('active', item === button));
            });

            form.querySelectorAll('input[type="range"]').forEach((input) => input.addEventListener('input', () => {
                input.closest('.field')?.querySelector('output')?.replaceChildren(`${input.value}${input.dataset.designUnit || ''}`);
            }));

            form.querySelector('[data-design-order]')?.addEventListener('click', (event) => {
                const item = event.target.closest('[data-order-item]');
                if (!item) return;
                if (event.target.closest('[data-order-up]') && item.previousElementSibling) item.before(item.previousElementSibling);
                if (event.target.closest('[data-order-down]') && item.nextElementSibling) item.after(item.nextElementSibling);
            });
        });
    </script>
@endpush
