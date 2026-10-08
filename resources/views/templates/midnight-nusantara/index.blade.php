@php
    $data = $invitation->data;
    $groom = data_get($data, 'groom', []);
    $bride = data_get($data, 'bride', []);
    $settings = data_get($data, 'settings', []);
    $events = data_get($data, 'events', []);
    $stories = data_get($data, 'stories', []);
    $banks = data_get($data, 'banks', []);
    $weddingDate = \Illuminate\Support\Carbon::parse(data_get($data, 'wedding_date'));
    $media = $invitation->media->groupBy('collection');
    $coverMedia = $media->get('cover')?->first();
    $groomPhoto = $media->get('groom')?->first();
    $bridePhoto = $media->get('bride')?->first();
    $storyPhotos = $media->get('story', collect())->values();
    $gallery = $media->get('gallery', collect());
    $qris = $media->get('qris')?->first();
    $musicMedia = $media->get('music')?->first();
    $musicVideoId = data_get($data, 'music.youtube_video_id')
        ?: ((!$musicMedia && data_get($settings, 'music', true)) ? config('music.default_youtube_video_id') : null);
    $canonical = $invitation instanceof \App\Models\TemplateDemo
        ? route('templates.show', $invitation->template)
        : route('public.invitation', $invitation->slug);
    $showAgenda = data_get($settings, 'countdown', true) || count($events);
    $showGift = data_get($settings, 'gift', true) && (count($banks) || $qris);
    $showRsvp = ! $isPreview && data_get($settings, 'rsvp', true);
    $showStory = data_get($settings, 'story', true) && count($stories);
    $showGallery = data_get($settings, 'gallery', true) && $gallery->isNotEmpty();
    $showWishes = data_get($settings, 'wishes', true);
    $chapter = 0;
    $prologueChapter = str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT);
    $coupleChapter = str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT);
    $agendaChapter = $showAgenda ? str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT) : null;
    $storyChapter = $showStory ? str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT) : null;
    $galleryChapter = $showGallery ? str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT) : null;
    $guestChapter = ($showRsvp || $showGift) ? str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT) : null;
    $wishesChapter = $showWishes ? str_pad((string) ++$chapter, 2, '0', STR_PAD_LEFT) : null;
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>{{ $invitation->title }} · {{ $weddingDate->translatedFormat('d F Y') }}</title>
    <meta name="description" content="Undangan pernikahan {{ data_get($groom, 'nickname') }} dan {{ data_get($bride, 'nickname') }} pada {{ $weddingDate->translatedFormat('d F Y') }}.">
    <meta name="theme-color" content="#07111f">
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="The Wedding of {{ data_get($groom, 'nickname') }} & {{ data_get($bride, 'nickname') }}">
    <meta property="og:description" content="{{ $weddingDate->translatedFormat('d F Y') }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if($coverMedia)<meta property="og:image" content="{{ asset('storage/'.$coverMedia->file_path) }}">@endif
    <link rel="stylesheet" href="{{ asset('css/templates/midnight-nusantara.css') }}">
    @if($musicVideoId)
        <link rel="stylesheet" href="{{ asset('css/youtube-player.css') }}">
        <script src="{{ asset('js/youtube-player.js') }}" defer></script>
    @endif
    <script src="{{ asset('js/templates/midnight-nusantara.js') }}" defer></script>
    <script src="{{ asset('js/invitation-forms.js') }}" defer></script>
</head>
<body>
    <div class="mn-shell" data-midnight-shell>
        <section class="mn-gate" aria-labelledby="gate-title">
            @if($coverMedia)
                <img class="mn-gate-photo" src="{{ asset('storage/'.$coverMedia->file_path) }}" alt="{{ $invitation->title }}" width="{{ $coverMedia->width }}" height="{{ $coverMedia->height }}" fetchpriority="high" decoding="async">
            @else
                <img class="mn-gate-photo" src="{{ asset('images/templates/midnight-nusantara/couple-placeholder.svg') }}" alt="" width="960" height="1280" fetchpriority="high" decoding="async">
            @endif
            <div class="mn-gate-wash"></div>
            <div class="mn-corner mn-corner-top" aria-hidden="true"></div>
            <div class="mn-corner mn-corner-bottom" aria-hidden="true"></div>
            <div class="mn-gate-panel">
                <p class="mn-eyebrow">Midnight Nusantara · No. 03</p>
                <div class="mn-seal" aria-hidden="true"><span>MN</span></div>
                <p class="mn-overline">The Wedding Celebration of</p>
                <h1 id="gate-title"><span>{{ data_get($bride, 'nickname') }}</span><b>&</b><span>{{ data_get($groom, 'nickname') }}</span></h1>
                <time datetime="{{ $weddingDate->toDateString() }}">{{ $weddingDate->format('d') }} · {{ strtoupper($weddingDate->translatedFormat('m')) }} · {{ $weddingDate->format('Y') }}</time>
                <div class="mn-address"><small>Undangan ini ditujukan kepada</small><strong>{{ $guestName !== '' ? $guestName : 'Tamu Undangan' }}</strong></div>
                <button class="mn-button mn-button-gold" id="open-invitation" type="button">Buka Undangan</button>
            </div>
        </section>

        <main tabindex="-1">
            <section class="mn-prologue mn-reveal" id="awal" data-mn-section="awal">
                <div class="mn-chapter"><span>Bab</span><strong>{{ $prologueChapter }}</strong></div>
                <div class="mn-prologue-copy">
                    <p class="mn-eyebrow">Sebuah Perayaan</p>
                    <h2>{{ data_get($bride, 'nickname') }} <i>&</i> {{ data_get($groom, 'nickname') }}</h2>
                    @if(data_get($data, 'quote'))<blockquote>“{{ data_get($data, 'quote') }}”</blockquote>@endif
                </div>
                <div class="mn-date-lockup" aria-label="{{ $weddingDate->translatedFormat('d F Y') }}">
                    <strong>{{ $weddingDate->format('d') }}</strong>
                    <span>{{ strtoupper($weddingDate->translatedFormat('F')) }}</span>
                    <span>{{ $weddingDate->format('Y') }}</span>
                </div>
            </section>

            <section class="mn-section mn-couple" id="pasangan" data-mn-section="pasangan">
                <header class="mn-section-head mn-reveal">
                    <div class="mn-chapter"><span>Bab</span><strong>{{ $coupleChapter }}</strong></div>
                    <div><p class="mn-eyebrow">Dua Nama · Satu Tujuan</p><h2>Kami yang berbahagia</h2></div>
                </header>
                <div class="mn-diptych">
                    @foreach([[$bride, $bridePhoto, 'Mempelai Wanita', 'A'], [$groom, $groomPhoto, 'Mempelai Pria', 'B']] as [$person, $photo, $label, $monogram])
                        <article class="mn-profile mn-reveal">
                            <figure>
                                @if($photo)
                                    <img src="{{ asset('storage/'.$photo->file_path) }}" alt="{{ data_get($person, 'full_name') }}" width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy" decoding="async">
                                @else
                                    <img src="{{ asset('images/templates/midnight-nusantara/couple-placeholder.svg') }}" alt="" width="960" height="1280" loading="lazy" decoding="async">
                                @endif
                                <figcaption>{{ $monogram }}</figcaption>
                            </figure>
                            <p class="mn-eyebrow">{{ $label }}</p>
                            <h3>{{ data_get($person, 'full_name') }}</h3>
                            <p>Putra/Putri dari<br><strong>{{ data_get($person, 'father') }}</strong><br>dan <strong>{{ data_get($person, 'mother') }}</strong></p>
                            @if(data_get($person, 'instagram'))<span class="mn-social">{{ data_get($person, 'instagram') }}</span>@endif
                        </article>
                    @endforeach
                    <div class="mn-union" aria-hidden="true"><span>&</span></div>
                </div>
            </section>

            @if($showAgenda)
                <section class="mn-section mn-agenda" id="agenda" data-mn-section="agenda">
                    <header class="mn-section-head mn-reveal">
                        <div class="mn-chapter"><span>Bab</span><strong>{{ $agendaChapter }}</strong></div>
                        <div><p class="mn-eyebrow">Wedding Programme</p><h2>Agenda Perayaan</h2></div>
                    </header>
                    @if(data_get($settings, 'countdown', true))
                        <div class="mn-countdown mn-reveal" data-countdown="{{ $weddingDate->copy()->startOfDay()->toIso8601String() }}">
                            @foreach([['days', 'Hari'], ['hours', 'Jam'], ['minutes', 'Menit'], ['seconds', 'Detik']] as [$unit, $label])
                                <div><strong data-{{ $unit }}>00</strong><span>{{ $label }}</span></div>
                            @endforeach
                        </div>
                    @endif
                    @if(count($events))
                        <div class="mn-programme">
                            @foreach($events as $event)
                                <article class="mn-event mn-reveal">
                                    <span class="mn-event-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div><p class="mn-eyebrow">{{ $event['name'] }}</p><h3>{{ \Illuminate\Support\Carbon::parse($event['date'])->translatedFormat('l, d F Y') }}</h3></div>
                                    <div class="mn-event-detail"><strong>{{ $event['time'] }} WIB</strong><span>{{ $event['location'] }}</span><p>{{ $event['address'] }}</p></div>
                                    @if($event['maps_url'] ?? null)<a class="mn-text-link" href="{{ $event['maps_url'] }}" target="_blank" rel="noopener noreferrer">Buka Peta <span aria-hidden="true">↗</span></a>@endif
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            @if($showStory)
                <section class="mn-section mn-chronicle" id="kisah" data-mn-section="kisah">
                    <header class="mn-section-head mn-reveal">
                        <div class="mn-chapter"><span>Bab</span><strong>{{ $storyChapter }}</strong></div>
                        <div><p class="mn-eyebrow">Catatan Perjalanan</p><h2>Kronik Kami</h2></div>
                    </header>
                    <div class="mn-stories">
                        @foreach($stories as $index => $story)
                            <article class="mn-story mn-reveal">
                                <span class="mn-story-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                @if($storyPhotos->get($index))
                                    <img src="{{ asset('storage/'.$storyPhotos->get($index)->file_path) }}" alt="{{ $story['title'] }}" width="{{ $storyPhotos->get($index)->width }}" height="{{ $storyPhotos->get($index)->height }}" loading="lazy" decoding="async">
                                @endif
                                <div><time @if(!empty($story['date'])) datetime="{{ $story['date'] }}" @endif>{{ !empty($story['date']) ? \Illuminate\Support\Carbon::parse($story['date'])->translatedFormat('d · m · Y') : '' }}</time><h3>{{ $story['title'] }}</h3><p>{{ $story['story'] }}</p></div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($showGallery)
                <section class="mn-section mn-gallery" id="galeri" data-mn-section="galeri">
                    <header class="mn-section-head mn-reveal">
                        <div class="mn-chapter"><span>Bab</span><strong>{{ $galleryChapter }}</strong></div>
                        <div><p class="mn-eyebrow">Night Archive</p><h2>Potongan Waktu</h2></div>
                    </header>
                    <div class="mn-gallery-grid mn-gallery-cols-{{ min($gallery->count(), 3) }}">
                        @foreach($gallery as $photo)
                            <figure class="mn-reveal" data-gallery-item>
                                <img src="{{ asset('storage/'.$photo->file_path) }}" alt="Momen {{ $invitation->title }} {{ $loop->iteration }}" width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy" decoding="async">
                                <figcaption>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($showRsvp || $showGift)
                <section class="mn-section mn-guest-desk" id="tamu" data-mn-section="tamu">
                    <header class="mn-section-head mn-reveal">
                        <div class="mn-chapter"><span>Bab</span><strong>{{ $guestChapter }}</strong></div>
                        <div><p class="mn-eyebrow">For Our Guests</p><h2>Meja Tamu</h2></div>
                    </header>
                    <div class="mn-guest-grid {{ !($showRsvp && $showGift) ? 'mn-single-panel' : '' }}">
                        @if($showRsvp)
                            <article class="mn-desk-panel mn-reveal" id="rsvp">
                                <span class="mn-panel-number">I</span><p class="mn-eyebrow">Konfirmasi</p><h3>Kehadiran Anda</h3><p>Kabar kehadiran membantu kami mempersiapkan perayaan dengan hangat.</p>
                                <p class="mn-form-status" data-form-status role="status" aria-live="polite" @if(!session('rsvp_success')) hidden @endif>{{ session('rsvp_success') }}</p>
                                <form method="POST" action="{{ route('public.rsvp', $invitation) }}" data-async-form="rsvp">
                                    @csrf
                                    <div class="mn-field"><label for="rsvp_name">Nama</label><input id="rsvp_name" name="guest_name" value="{{ old('guest_name', $guestName) }}" autocomplete="name" aria-describedby="rsvp_name_error" required><small id="rsvp_name_error" data-field-error="guest_name"></small></div>
                                    <div class="mn-field"><label for="attendance">Kehadiran</label><select id="attendance" name="attendance" aria-describedby="attendance_error" required><option value="attending" @selected(old('attendance', 'attending') === 'attending')>Hadir</option><option value="not_attending" @selected(old('attendance') === 'not_attending')>Tidak hadir</option></select><small id="attendance_error" data-field-error="attendance"></small></div>
                                    <div class="mn-field" data-guest-count-field><label for="guest_count">Jumlah tamu</label><input id="guest_count" type="number" name="guest_count" min="1" max="10" value="{{ old('guest_count', 1) }}" aria-describedby="guest_count_error"><small id="guest_count_error" data-field-error="guest_count"></small></div>
                                    <button class="mn-button mn-button-gold" type="submit">Kirim Konfirmasi</button>
                                </form>
                            </article>
                        @endif
                        @if($showGift)
                            <article class="mn-desk-panel mn-reveal">
                                <span class="mn-panel-number">II</span><p class="mn-eyebrow">Tanda Kasih</p><h3>Wedding Gift</h3><p>Doa restu Anda adalah hadiah utama. Tanda kasih dapat disampaikan melalui:</p>
                                @foreach($banks as $bank)
                                    <div class="mn-bank"><span>{{ $bank['bank_name'] }}</span><strong>{{ $bank['account_number'] }}</strong><small>a.n. {{ $bank['account_name'] }}</small><button type="button" data-account="{{ $bank['account_number'] }}">Salin nomor</button></div>
                                @endforeach
                                @if($qris)<img class="mn-qris" src="{{ asset('storage/'.$qris->file_path) }}" alt="QRIS wedding gift" width="{{ $qris->width }}" height="{{ $qris->height }}" loading="lazy" decoding="async">@endif
                            </article>
                        @endif
                    </div>
                </section>
            @endif

            @if($showWishes)
                <section class="mn-section mn-guestbook" id="ucapan" data-mn-section="ucapan">
                    <header class="mn-section-head mn-reveal">
                        <div class="mn-chapter"><span>Bab</span><strong>{{ $wishesChapter }}</strong></div>
                        <div><p class="mn-eyebrow">Guest Ledger</p><h2>Buku Ucapan</h2></div>
                    </header>
                    <div class="mn-ledger">
                        <div class="mn-wish-list" data-wish-list>
                            @forelse($invitation->wishes as $wish)
                                <article class="mn-reveal"><strong>{{ $wish->guest_name }}</strong><p>{{ $wish->message }}</p><time datetime="{{ $wish->created_at->toIso8601String() }}">{{ $wish->created_at->diffForHumans() }}</time></article>
                            @empty
                                <p class="mn-empty">Jadilah yang pertama meninggalkan doa dan harapan.</p>
                            @endforelse
                        </div>
                        @if(!$isPreview)
                            <div class="mn-wish-form mn-reveal">
                                <p class="mn-form-status" data-form-status role="status" aria-live="polite" hidden></p>
                                <form method="POST" action="{{ route('public.wishes', $invitation) }}" data-async-form="wish">
                                    @csrf
                                    <div class="mn-field"><label for="wish_name">Nama</label><input id="wish_name" name="guest_name" value="{{ old('guest_name', $guestName) }}" autocomplete="name" aria-describedby="wish_name_error" required><small id="wish_name_error" data-field-error="guest_name"></small></div>
                                    <div class="mn-field"><label for="message">Pesan dan doa</label><textarea id="message" name="message" rows="5" aria-describedby="message_error" required></textarea><small id="message_error" data-field-error="message"></small></div>
                                    <button class="mn-button mn-button-outline" type="submit">Tulis di Buku Tamu</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <footer class="mn-closing mn-reveal">
                <div class="mn-closing-seal" aria-hidden="true"><span>{{ mb_substr(data_get($bride, 'nickname', ''), 0, 1) }}{{ mb_substr(data_get($groom, 'nickname', ''), 0, 1) }}</span></div>
                <p class="mn-eyebrow">Terima Kasih</p>
                <h2>Sampai berjumpa<br>di perayaan kami.</h2>
                <p>Merupakan kehormatan bagi kami apabila Anda berkenan hadir dan memberikan doa restu.</p>
                <strong>{{ data_get($bride, 'nickname') }} & {{ data_get($groom, 'nickname') }}</strong>
                <time datetime="{{ $weddingDate->toDateString() }}">{{ $weddingDate->translatedFormat('d F Y') }}</time>
                <small>Made with care · {{ config('app.name') }}</small>
            </footer>
        </main>

        <nav class="mn-nav" aria-label="Navigasi undangan">
            <a href="#awal" data-nav-link="awal" class="active">Awal</a>
            <a href="#pasangan" data-nav-link="pasangan">Pasangan</a>
            @if($showAgenda)<a href="#agenda" data-nav-link="agenda">Agenda</a>@endif
            @if($showRsvp || $showGift)<a href="#tamu" data-nav-link="tamu">Tamu</a>@endif
        </nav>
    </div>

    @include('templates.partials.youtube-music')
    @if(!$musicVideoId && $musicMedia && data_get($settings, 'music', true))
        <audio id="wedding-music" loop preload="none" data-start-seconds="{{ (int) data_get($data, 'music.start_seconds', 0) }}"><source src="{{ asset('storage/'.$musicMedia->file_path) }}"></audio>
        <button class="music-toggle" type="button" aria-label="Putar atau jeda musik" hidden><span></span></button>
    @endif
</body>
</html>
