@php
    $data = old() ?: $invitation?->data ?? [];
    $events = old('events', data_get($data, 'events', []));
    $stories = old('stories', data_get($data, 'stories', []));
    $banks = old('banks', data_get($data, 'banks', []));
    $demoWishes = old('wishes', data_get($data, 'wishes', []));
    $isDemoEditor = $isDemoEditor ?? false;
    $selectedTemplateId = old('template_id', $invitation?->template_id ?? request('template_id'));
    $errorStep = 1;
    foreach ($errors->keys() as $errorKey) {
        $errorStep = match (true) {
            str_starts_with($errorKey, 'groom.'), str_starts_with($errorKey, 'bride.') => 2,
            $errorKey === 'wedding_date', $errorKey === 'quote', str_starts_with($errorKey, 'events.') => 3,
            str_starts_with($errorKey, 'stories.'), str_starts_with($errorKey, 'banks.') => 4,
            in_array($errorKey, ['cover', 'groom_photo', 'bride_photo', 'qris', 'gallery', 'story_photos', 'wedding_music_id', 'youtube_url'], true) => 5,
            str_starts_with($errorKey, 'settings.'), str_starts_with($errorKey, 'wishes.') => 6,
            default => 1,
        };
        break;
    }
@endphp

<div class="invitation-wizard" data-invitation-wizard data-initial-step="{{ $errorStep }}">
    <nav class="invitation-wizard-nav" aria-label="Langkah pengisian undangan">
        @foreach ([1 => 'Informasi', 2 => 'Mempelai', 3 => 'Acara', 4 => 'Cerita & Hadiah', 5 => 'Media & Musik', 6 => 'Konfirmasi'] as $step => $label)
            <button type="button" data-wizard-tab="{{ $step }}" @if ($step === $errorStep) aria-current="step" @endif>
                <span>{{ $step }}</span><small>{{ $label }}</small>
            </button>
        @endforeach
    </nav>

    <div class="invitation-wizard-progress" aria-hidden="true"><span data-wizard-progress></span></div>

    <section class="panel invitation-step" data-wizard-step="1">
        <header class="invitation-step-header">
            <span>1</span><div><p class="overline">Langkah 1 dari 6</p><h2>Informasi Undangan</h2><p>Tentukan template, judul, dan alamat link undangan.</p></div>
        </header>
        <div class="form-grid">
            <div class="field">
                <label for="template_id">Template <em>Wajib</em></label>
                <select class="input" id="template_id" name="template_id" required @disabled($isDemoEditor)>
                    <option value="">Pilih template</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}" @selected((string) $selectedTemplateId === (string) $template->id)>{{ $template->name }}</option>
                    @endforeach
                </select>
                @if ($isDemoEditor)<input type="hidden" name="template_id" value="{{ $invitation->template_id }}">@endif
                @error('template_id')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="field">
                <label for="title">Judul undangan <em>Wajib</em></label>
                <input class="input" id="title" name="title" data-title-source data-summary-source="title"
                    value="{{ old('title', $invitation?->title) }}" placeholder="Contoh: Alexander & Cyntia" maxlength="150" required>
                <small>Nama ini tampil sebagai judul undangan.</small>
                @error('title')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="field full">
                <label for="slug">Alamat link <em>Wajib</em></label>
                <div class="slug-input"><span>{{ url('/') }}/</span><input class="input" id="slug" name="slug"
                        data-slug-target data-summary-source="slug" value="{{ old('slug', $invitation?->slug) }}"
                        placeholder="alexander-cyntia" maxlength="150" required></div>
                <small>Gunakan nama singkat tanpa spasi. Tautan dapat dibagikan setelah undangan dipublikasikan.</small>
                @error('slug')<small class="field-error">{{ $message }}</small>@enderror
            </div>
        </div>
    </section>

    <section class="panel invitation-step" data-wizard-step="2">
        <header class="invitation-step-header">
            <span>2</span><div><p class="overline">Langkah 2 dari 6</p><h2>Data Kedua Mempelai</h2><p>Nama panggilan paling penting. Informasi keluarga dapat dilengkapi nanti.</p></div>
        </header>
        <div class="couple-form-grid">
            @foreach (['groom' => ['Mempelai Pria', 'Nama pria'], 'bride' => ['Mempelai Wanita', 'Nama wanita']] as $person => [$heading, $placeholder])
                <div class="couple-form-card">
                    <div class="couple-form-title"><span>{{ $loop->iteration }}</span><h3>{{ $heading }}</h3></div>
                    <div class="field"><label for="{{ $person }}_nickname">Nama panggilan <em>Disarankan</em></label><input class="input" id="{{ $person }}_nickname" name="{{ $person }}[nickname]" data-summary-source="{{ $person }}" value="{{ old($person . '.nickname', data_get($data, $person . '.nickname')) }}" placeholder="{{ $placeholder }}" maxlength="80">@error($person . '.nickname')<small class="field-error">{{ $message }}</small>@enderror</div>
                    <div class="field"><label for="{{ $person }}_full_name">Nama lengkap</label><input class="input" id="{{ $person }}_full_name" name="{{ $person }}[full_name]" value="{{ old($person . '.full_name', data_get($data, $person . '.full_name')) }}" placeholder="Nama lengkap beserta gelar" maxlength="150"></div>
                    <div class="form-grid compact-form-grid">
                        <div class="field"><label for="{{ $person }}_father">Nama ayah</label><input class="input" id="{{ $person }}_father" name="{{ $person }}[father]" value="{{ old($person . '.father', data_get($data, $person . '.father')) }}" maxlength="150"></div>
                        <div class="field"><label for="{{ $person }}_mother">Nama ibu</label><input class="input" id="{{ $person }}_mother" name="{{ $person }}[mother]" value="{{ old($person . '.mother', data_get($data, $person . '.mother')) }}" maxlength="150"></div>
                    </div>
                    <div class="field"><label for="{{ $person }}_instagram">Instagram <em>Opsional</em></label><input class="input" id="{{ $person }}_instagram" name="{{ $person }}[instagram]" value="{{ old($person . '.instagram', data_get($data, $person . '.instagram')) }}" placeholder="@username" maxlength="100"></div>
                    <div class="field"><label for="{{ $person }}_photo">Foto mempelai <em>Opsional</em></label><input class="input" id="{{ $person }}_photo" type="file" name="{{ $person }}_photo" accept="image/jpeg,image/png,image/webp" data-max-mb="5"><small>JPG, PNG, atau WebP. Maksimal 5 MB.</small></div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="panel invitation-step" data-wizard-step="3">
        <header class="invitation-step-header">
            <span>3</span><div><p class="overline">Langkah 3 dari 6</p><h2>Tanggal dan Acara</h2><p>Tambahkan akad, resepsi, atau acara lain yang akan dihadiri tamu.</p></div>
        </header>
        <div class="form-grid">
            <div class="field"><label for="wedding_date">Tanggal pernikahan <em>Disarankan</em></label><input class="input" id="wedding_date" type="date" name="wedding_date" data-summary-source="date" value="{{ old('wedding_date', data_get($data, 'wedding_date')) }}">@error('wedding_date')<small class="field-error">{{ $message }}</small>@enderror</div>
            <div class="field full">
                <label for="quote">Kutipan pembuka <em>Opsional</em></label>
                <div class="quote-presets" data-quote-presets>
                    @foreach ([
                        'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan pasangan-pasangan untukmu agar kamu merasa tenteram kepadanya, dan Dia menjadikan di antaramu rasa kasih dan sayang.',
                        'Cinta bukan tentang menemukan seseorang yang sempurna, tetapi belajar melihat seseorang yang tidak sempurna dengan cara yang sempurna.',
                        'Dua jiwa, satu perjalanan, dan cinta yang tumbuh selamanya.',
                        'Dengan penuh rasa syukur, kami mengundang Anda untuk menjadi bagian dari awal perjalanan hidup kami bersama.',
                        'Di antara ribuan langkah, Tuhan mempertemukan kami untuk berjalan berdampingan menuju masa depan.',
                        'Semoga Allah menghimpun yang terserak dari keduanya, memberkahi mereka, dan membuka pintu rahmat serta rezeki.',
                        'Hari ini kami memilih satu sama lain. Esok dan seterusnya, kami memilih untuk terus bertumbuh bersama.',
                        'Cinta yang sederhana menjadi istimewa ketika dua hati berjanji untuk saling menjaga selamanya.',
                    ] as $preset)
                        <button type="button" data-quote-value="{{ $preset }}">{{ \Illuminate\Support\Str::limit($preset, 90) }}</button>
                    @endforeach
                    <button type="button" data-quote-custom>Tulis sendiri</button>
                </div>
                <textarea class="input" id="quote" name="quote" maxlength="500" placeholder="Pilih salah satu kutipan atau tulis kutipan sendiri">{{ old('quote', data_get($data, 'quote')) }}</textarea>
                <small>Klik salah satu pilihan. Teks yang terisi tetap dapat Anda ubah.</small>
            </div>
        </div>
        <div class="invitation-subsection" data-repeater data-next-index="{{ count($events) }}" data-repeater-name="Acara">
            <div class="invitation-subsection-head"><div><h3>Daftar Acara</h3><p>Minimal satu acara diperlukan sebelum publikasi.</p></div><button class="button small secondary" type="button" data-add-repeater>+ Tambah Acara</button></div>
            <div class="repeater-empty" data-repeater-empty><strong>Belum ada acara</strong><span>Tambahkan akad atau resepsi agar tamu mengetahui jadwal.</span></div>
            <div data-repeater-list>
                @foreach ($events as $index => $event)
                    <div class="repeater-item">
                        <div class="repeater-item-head"><strong data-repeater-title>Acara {{ $loop->iteration }}</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div>
                        <div class="form-grid">
                            <div class="field"><label>Nama acara <em>Wajib</em></label><input class="input" name="events[{{ $index }}][name]" value="{{ $event['name'] ?? '' }}" placeholder="Akad Nikah" required></div>
                            <div class="field"><label>Lokasi <em>Wajib</em></label><input class="input" name="events[{{ $index }}][location]" value="{{ $event['location'] ?? '' }}" placeholder="Nama gedung atau tempat" required></div>
                            <div class="field"><label>Tanggal <em>Wajib</em></label><input class="input" type="date" name="events[{{ $index }}][date]" value="{{ $event['date'] ?? '' }}" required></div>
                            <div class="field"><label>Jam <em>Wajib</em></label><input class="input" type="time" name="events[{{ $index }}][time]" value="{{ $event['time'] ?? '' }}" required></div>
                            <div class="field full"><label>Alamat <em>Wajib</em></label><textarea class="input" name="events[{{ $index }}][address]" placeholder="Alamat lengkap lokasi acara" required>{{ $event['address'] ?? '' }}</textarea></div>
                            <div class="field full"><label>Link Google Maps <em>Opsional</em></label><input class="input" type="url" name="events[{{ $index }}][maps_url]" value="{{ $event['maps_url'] ?? '' }}" placeholder="https://maps.google.com/..."></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <template><div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Acara</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid"><div class="field"><label>Nama acara <em>Wajib</em></label><input class="input" name="events[__INDEX__][name]" placeholder="Akad Nikah" required></div><div class="field"><label>Lokasi <em>Wajib</em></label><input class="input" name="events[__INDEX__][location]" placeholder="Nama gedung atau tempat" required></div><div class="field"><label>Tanggal <em>Wajib</em></label><input class="input" type="date" name="events[__INDEX__][date]" required></div><div class="field"><label>Jam <em>Wajib</em></label><input class="input" type="time" name="events[__INDEX__][time]" required></div><div class="field full"><label>Alamat <em>Wajib</em></label><textarea class="input" name="events[__INDEX__][address]" placeholder="Alamat lengkap lokasi acara" required></textarea></div><div class="field full"><label>Link Google Maps <em>Opsional</em></label><input class="input" type="url" name="events[__INDEX__][maps_url]" placeholder="https://maps.google.com/..."></div></div></div></template>
        </div>
    </section>

    <section class="panel invitation-step" data-wizard-step="4">
        <header class="invitation-step-header">
            <span>4</span><div><p class="overline">Langkah 4 dari 6</p><h2>Cerita dan Hadiah</h2><p>Bagian ini opsional. Lewati bila belum ingin ditampilkan.</p></div>
        </header>
        <div class="invitation-subsection" data-repeater data-next-index="{{ count($stories) }}" data-repeater-name="Cerita">
            <div class="invitation-subsection-head"><div><h3>Love Story</h3><p>Ceritakan momen penting perjalanan Anda.</p></div><button class="button small secondary" type="button" data-add-repeater>+ Tambah Cerita</button></div>
            <div class="repeater-empty" data-repeater-empty><strong>Belum ada cerita</strong><span>Bagian Love Story tidak ditampilkan jika dikosongkan.</span></div>
            <div data-repeater-list>
                @foreach ($stories as $index => $story)
                    <div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Cerita {{ $loop->iteration }}</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid"><div class="field"><label>Judul <em>Wajib</em></label><input class="input" name="stories[{{ $index }}][title]" value="{{ $story['title'] ?? '' }}" placeholder="Pertama Bertemu" required></div><div class="field"><label>Tanggal <em>Opsional</em></label><input class="input" type="date" name="stories[{{ $index }}][date]" value="{{ $story['date'] ?? '' }}"></div><div class="field full"><label>Cerita <em>Wajib</em></label><textarea class="input" name="stories[{{ $index }}][story]" placeholder="Ceritakan momen ini secara singkat" required>{{ $story['story'] ?? '' }}</textarea></div></div></div>
                @endforeach
            </div>
            <template><div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Cerita</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid"><div class="field"><label>Judul <em>Wajib</em></label><input class="input" name="stories[__INDEX__][title]" placeholder="Pertama Bertemu" required></div><div class="field"><label>Tanggal <em>Opsional</em></label><input class="input" type="date" name="stories[__INDEX__][date]"></div><div class="field full"><label>Cerita <em>Wajib</em></label><textarea class="input" name="stories[__INDEX__][story]" placeholder="Ceritakan momen ini secara singkat" required></textarea></div></div></div></template>
            <div class="field repeater-media-field"><label>Foto Love Story <em>Opsional</em></label><input class="input" type="file" name="story_photos[]" accept="image/jpeg,image/png,image/webp" data-max-mb="5" data-max-files="10" multiple><small>Urutan foto mengikuti urutan cerita. Maksimal 10 foto.</small></div>
        </div>
        <div class="invitation-subsection" data-repeater data-next-index="{{ count($banks) }}" data-repeater-name="Rekening">
            <div class="invitation-subsection-head"><div><h3>Wedding Gift</h3><p>Tambahkan rekening jika ingin menerima hadiah digital.</p></div><button class="button small secondary" type="button" data-add-repeater>+ Tambah Rekening</button></div>
            <div class="repeater-empty" data-repeater-empty><strong>Belum ada rekening</strong><span>Bagian rekening tidak ditampilkan jika dikosongkan.</span></div>
            <div data-repeater-list>
                @foreach ($banks as $index => $bank)
                    <div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Rekening {{ $loop->iteration }}</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid three-columns"><div class="field"><label>Bank <em>Wajib</em></label><input class="input" name="banks[{{ $index }}][bank_name]" value="{{ $bank['bank_name'] ?? '' }}" placeholder="BCA" required></div><div class="field"><label>Nomor rekening <em>Wajib</em></label><input class="input" name="banks[{{ $index }}][account_number]" value="{{ $bank['account_number'] ?? '' }}" inputmode="numeric" required></div><div class="field"><label>Nama pemilik <em>Wajib</em></label><input class="input" name="banks[{{ $index }}][account_name]" value="{{ $bank['account_name'] ?? '' }}" required></div></div></div>
                @endforeach
            </div>
            <template><div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Rekening</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid three-columns"><div class="field"><label>Bank <em>Wajib</em></label><input class="input" name="banks[__INDEX__][bank_name]" placeholder="BCA" required></div><div class="field"><label>Nomor rekening <em>Wajib</em></label><input class="input" name="banks[__INDEX__][account_number]" inputmode="numeric" required></div><div class="field"><label>Nama pemilik <em>Wajib</em></label><input class="input" name="banks[__INDEX__][account_name]" required></div></div></div></template>
        </div>
    </section>

    <section class="panel invitation-step" data-wizard-step="5">
        <header class="invitation-step-header">
            <span>5</span><div><p class="overline">Langkah 5 dari 6</p><h2>Foto dan Musik</h2><p>Hidupkan undangan dengan foto terbaik dan musik pilihan.</p></div>
        </header>
        <div class="invitation-subsection">
            <div class="invitation-subsection-head"><div><h3>Media Undangan</h3><p>Semua media opsional dan dapat ditambahkan kembali nanti.</p></div></div>
            @if ($isDemoEditor && $invitation->media->isNotEmpty())
                <div class="demo-media-library">
                    @foreach ($invitation->media->groupBy('collection') as $collection => $items)
                        <article>
                            <div><strong>{{ match ($collection) { 'cover' => 'Cover', 'groom' => 'Foto Pria', 'bride' => 'Foto Wanita', 'qris' => 'QRIS', 'gallery' => 'Galeri', 'story' => 'Foto Cerita', default => ucfirst($collection) } }}</strong><small>{{ $items->count() }} file tersimpan</small></div>
                            <div class="demo-media-previews">
                                @foreach ($items as $media)
                                    <img src="{{ asset('storage/' . $media->file_path) }}" alt="Media {{ $collection }}" loading="lazy">
                                @endforeach
                            </div>
                            <label class="demo-remove-media"><input type="checkbox" name="remove_media[]" value="{{ $collection }}"> Hapus media ini</label>
                        </article>
                    @endforeach
                </div>
            @elseif ($isDemoEditor)
                <div class="repeater-empty"><strong>Belum ada media demo</strong><span>Unggah foto untuk membuat tampilan demo lebih hidup.</span></div>
            @endif
            @if ($invitation && ! $isDemoEditor)
                <div class="editor-note">Unggah file baru hanya akan mengganti media pada kategori yang dipilih.</div>
            @elseif ($isDemoEditor)
                <div class="editor-note">Unggahan baru mengganti seluruh file pada kategori terkait. Centang hapus untuk mengosongkan kategori.</div>
            @endif
            <div class="form-grid media-upload-grid">
                <div class="field"><label>Foto cover</label><input class="input" type="file" name="cover" accept="image/jpeg,image/png,image/webp" data-max-mb="5"><small>Foto utama pada halaman pembuka.</small></div>
                <div class="field"><label>QRIS</label><input class="input" type="file" name="qris" accept="image/jpeg,image/png,image/webp" data-max-mb="3"><small>Maksimal 3 MB.</small></div>
                <div class="field full"><label>Galeri foto</label><input class="input" type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp" data-max-mb="5" data-max-files="12" multiple><small>Pilih maksimal 12 foto sekaligus.</small></div>
            </div>
        </div>
        <div class="invitation-subsection">
            <div class="invitation-subsection-head"><div><h3>Musik Undangan</h3><p>Pilih satu musik katalog atau gunakan link YouTube sendiri.</p></div></div>
            <div class="music-picker invitation-music-picker">
                <label class="music-choice music-choice-none"><input type="radio" name="wedding_music_id" value="" @checked(!old('wedding_music_id', $invitation?->wedding_music_id))><span><strong>Tanpa musik katalog</strong><small>Gunakan link sendiri atau tampilkan tanpa musik.</small></span></label>
                @foreach ($musicChoices as $song)
                    <div class="music-choice"><label><input type="radio" name="wedding_music_id" value="{{ $song->id }}" @checked((string) old('wedding_music_id', $invitation?->wedding_music_id) === (string) $song->id)><img src="{{ $song->thumbnail }}" alt="" loading="lazy"><span><strong>{{ $song->title }}</strong><small>{{ $song->category }}</small></span></label><button class="button secondary small" type="button" data-youtube-preview="{{ $song->youtube_video_id }}">Pratinjau</button></div>
                @endforeach
            </div>
            <div class="music-custom-divider"><span>atau gunakan link sendiri</span></div>
            <div class="field"><label for="youtube_url">Tautan YouTube <em>Opsional</em></label><input class="input" id="youtube_url" name="youtube_url" type="url" value="{{ old('youtube_url', $invitation?->wedding_music_id ? '' : data_get($data, 'music.youtube_url')) }}" placeholder="https://www.youtube.com/watch?v=..."><small>Tautan ini diprioritaskan jika musik katalog juga dipilih.</small>@error('youtube_url')<small class="field-error">{{ $message }}</small>@enderror</div>
        </div>
    </section>

    <section class="panel invitation-step" data-wizard-step="6">
        <header class="invitation-step-header">
            <span>6</span><div><p class="overline">Langkah 6 dari 6</p><h2>Periksa dan Simpan</h2><p>Pilih bagian yang ingin ditampilkan lalu periksa ringkasan.</p></div>
        </header>
        <div class="invitation-settings">
            <h3>Bagian yang Ditampilkan</h3>
            <div class="check-grid">
                @foreach (['countdown' => 'Hitung Mundur', 'gallery' => 'Galeri', 'story' => 'Love Story', 'gift' => 'Wedding Gift', 'rsvp' => 'RSVP', 'wishes' => 'Ucapan', 'music' => 'Musik'] as $key => $label)
                    <label class="check"><input type="checkbox" name="settings[{{ $key }}]" value="1" @checked((bool) old('settings.' . $key, data_get($data, 'settings.' . $key, true)))><span>{{ $label }}</span></label>
                @endforeach
            </div>
        </div>
        @if ($isDemoEditor)
            <div class="invitation-subsection" data-repeater data-next-index="{{ count($demoWishes) }}" data-repeater-name="Ucapan">
                <div class="invitation-subsection-head"><div><h3>Contoh Ucapan Tamu</h3><p>Ucapan ini tampil pada preview demo untuk memberi gambaran tampilan.</p></div><button class="button small secondary" type="button" data-add-repeater>+ Tambah Ucapan</button></div>
                <div class="repeater-empty" data-repeater-empty><strong>Belum ada contoh ucapan</strong><span>Tambahkan beberapa pesan agar bagian Ucapan tidak kosong.</span></div>
                <div data-repeater-list>
                    @foreach ($demoWishes as $index => $wish)
                        <div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Ucapan {{ $loop->iteration }}</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid"><div class="field"><label>Nama tamu <em>Wajib</em></label><input class="input" name="wishes[{{ $index }}][guest_name]" value="{{ $wish['guest_name'] ?? '' }}" required maxlength="100"></div><div class="field full"><label>Pesan <em>Wajib</em></label><textarea class="input" name="wishes[{{ $index }}][message]" required maxlength="1000">{{ $wish['message'] ?? '' }}</textarea></div></div></div>
                    @endforeach
                </div>
                <template><div class="repeater-item"><div class="repeater-item-head"><strong data-repeater-title>Ucapan</strong><button class="button small danger" type="button" data-remove-repeater>Hapus</button></div><div class="form-grid"><div class="field"><label>Nama tamu <em>Wajib</em></label><input class="input" name="wishes[__INDEX__][guest_name]" required maxlength="100"></div><div class="field full"><label>Pesan <em>Wajib</em></label><textarea class="input" name="wishes[__INDEX__][message]" required maxlength="1000"></textarea></div></div></div></template>
            </div>
        @endif
        <div class="invitation-review">
            <div><span>Template</span><strong data-summary="template">-</strong></div>
            <div><span>Judul</span><strong data-summary="title">Belum diisi</strong></div>
            <div><span>Tautan undangan</span><strong data-summary="slug">Belum diisi</strong></div>
            <div><span>Mempelai</span><strong data-summary="couple">Belum diisi</strong></div>
            <div><span>Tanggal</span><strong data-summary="date">Belum diisi</strong></div>
            <div><span>Jumlah acara</span><strong data-summary="events">0 acara</strong></div>
        </div>
        <div class="editor-note">Undangan disimpan sebagai draft. Anda dapat preview, mengubah data, lalu mempublikasikannya setelah semua informasi lengkap.</div>
    </section>

    <div class="invitation-wizard-actions">
        <button class="button secondary" type="button" data-wizard-previous>Kembali</button>
        <span data-wizard-status>Langkah {{ $errorStep }} dari 6</span>
        <button class="button gold" type="button" data-wizard-next>Lanjutkan</button>
        <button class="button gold" type="submit" data-wizard-submit>{{ $isDemoEditor ? 'Simpan Demo' : ($invitation ? 'Simpan Perubahan' : 'Buat Undangan') }}</button>
    </div>
</div>

<dialog class="app-dialog video-dialog" data-video-dialog><button class="icon-button dialog-close" type="button"
        data-dialog-close aria-label="Tutup">×</button><div data-video-frame></div></dialog>
