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
    $musicVideoId =
        data_get($data, 'music.youtube_video_id') ?:
        (!$musicMedia && data_get($settings, 'music', true)
            ? config('music.default_youtube_video_id')
            : null);
    $canonical =
        $invitation instanceof \App\Models\TemplateDemo
            ? route('templates.show', $invitation->template)
            : route('public.invitation', $invitation->slug);
    $showRsvp = !$isPreview && data_get($settings, 'rsvp', true);
    $showGift = data_get($settings, 'gift', true) && (count($banks) || $qris);
@endphp
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>{{ $invitation->title }} · {{ $weddingDate->translatedFormat('d F Y') }}</title>
    <meta name="description"
        content="Undangan pernikahan {{ data_get($bride, 'nickname') }} dan {{ data_get($groom, 'nickname') }} pada {{ $weddingDate->translatedFormat('d F Y') }}.">
    <meta name="theme-color" content="#8fd7ff">
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="The Wedding of {{ data_get($bride, 'nickname') }} & {{ data_get($groom, 'nickname') }}">
    <meta property="og:description" content="{{ $weddingDate->translatedFormat('d F Y') }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($coverMedia)
        <meta property="og:image" content="{{ asset('storage/' . $coverMedia->file_path) }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/templates/voxel-voyage.css') }}">
    @if ($musicVideoId)
        <link rel="stylesheet" href="{{ asset('css/youtube-player.css') }}">
        <script src="{{ asset('js/youtube-player.js') }}" defer></script>
    @endif
    <script src="{{ asset('js/templates/voxel-voyage.js') }}" defer></script>
    <script src="{{ asset('js/invitation-forms.js') }}" defer></script>
</head>

<body>
    <section class="vv-gate" aria-labelledby="vv-gate-title">
        @if ($coverMedia)
            <img class="vv-gate-photo" src="{{ asset('storage/' . $coverMedia->file_path) }}"
                alt="{{ $invitation->title }}" width="{{ $coverMedia->width }}" height="{{ $coverMedia->height }}"
                fetchpriority="high" decoding="async">
        @endif
        <div class="vv-sky" aria-hidden="true"><i></i><i></i><i></i></div>
        <div class="vv-gate-card">
            <span class="vv-label">New Journey Unlocked</span>
            <h1 id="vv-gate-title">{{ data_get($bride, 'nickname') }}<b>+</b>{{ data_get($groom, 'nickname') }}</h1>
            <time datetime="{{ $weddingDate->toDateString() }}">{{ $weddingDate->translatedFormat('d F Y') }}</time>
            <div class="vv-guest"><small>Player
                    invited</small><strong>{{ $guestName !== '' ? $guestName : 'Tamu Undangan' }}</strong></div>
            <button class="vv-button vv-primary" id="open-invitation" type="button">Mulai Perjalanan</button>
        </div>
        <div class="vv-terrain" aria-hidden="true"></div>
    </section>

    <div class="vv-app" data-voxel-voyage>
        <nav class="vv-hud" aria-label="Navigasi undangan">
            <a href="#spawn" data-vv-nav="spawn" class="active"><span>01</span>Spawn</a>
            <a href="#partners" data-vv-nav="partners"><span>02</span>Partners</a>
            @if (count($events))
                <a href="#missions" data-vv-nav="missions"><span>03</span>Missions</a>
            @endif
            @if ($showRsvp || $showGift)
                <a href="#station" data-vv-nav="station"><span>04</span>Station</a>
            @endif
        </nav>

        <main tabindex="-1">
            <section class="vv-section vv-spawn" id="spawn" data-vv-section="spawn">
                <div class="vv-section-title vv-reveal"><span>Spawn Point</span>
                    <h2>Petualangan baru dimulai.</h2>
                </div>
                <div class="vv-spawn-grid">
                    <article class="vv-tile vv-date vv-reveal"><small>Wedding
                            Day</small><strong>{{ $weddingDate->format('d') }}</strong><span>{{ strtoupper($weddingDate->translatedFormat('F Y')) }}</span>
                    </article>
                    <article class="vv-tile vv-quote vv-reveal"><span aria-hidden="true">“</span>
                        <blockquote>
                            {{ data_get($data, 'quote', 'Dua pemain, satu dunia, satu perjalanan selamanya.') }}
                        </blockquote>
                    </article>
                    @if (data_get($settings, 'countdown', true))
                        <article class="vv-tile vv-countdown vv-reveal"
                            data-countdown="{{ $weddingDate->copy()->startOfDay()->toIso8601String() }}">
                            @foreach ([['days', 'Hari'], ['hours', 'Jam'], ['minutes', 'Menit'], ['seconds', 'Detik']] as [$unit, $label])
                                <div><strong data-{{ $unit }}>00</strong><span>{{ $label }}</span>
                                </div>
                            @endforeach
                        </article>
                    @endif
                    <article class="vv-tile vv-title-tile vv-reveal"><small>Co-op celebration</small>
                        <h2>{{ data_get($bride, 'nickname') }} <b>&</b> {{ data_get($groom, 'nickname') }}</h2>
                    </article>
                </div>
            </section>

            <section class="vv-section vv-partners" id="partners" data-vv-section="partners">
                <div class="vv-section-title vv-reveal"><span>Co-op Partners</span>
                    <h2>Player utama</h2>
                </div>
                <div class="vv-player-grid">
                    @foreach ([[$bride, $bridePhoto, 'Player One'], [$groom, $groomPhoto, 'Player Two']] as [$person, $photo, $role])
                        <article class="vv-player vv-reveal">
                            <div class="vv-avatar">
                                @if ($photo)
                                    <img src="{{ asset('storage/' . $photo->file_path) }}"
                                        alt="{{ data_get($person, 'full_name') }}" width="{{ $photo->width }}"
                                        height="{{ $photo->height }}" loading="lazy" decoding="async">
                                @else
                                    <span>{{ mb_strtoupper(mb_substr(data_get($person, 'nickname', 'P'), 0, 1)) }}</span>
                                @endif
                            </div>
                            <div><small>{{ $role }}</small>
                                <h3>{{ data_get($person, 'full_name') }}</h3>
                                <p>Putra/Putri dari<br><strong>{{ data_get($person, 'father') }}</strong><br>dan
                                    <strong>{{ data_get($person, 'mother') }}</strong></p>
                                @if (data_get($person, 'instagram'))
                                    <em>{{ data_get($person, 'instagram') }}</em>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            @if (count($events))
                <section class="vv-section vv-missions" id="missions" data-vv-section="missions">
                    <div class="vv-section-title vv-reveal"><span>Mission Board</span>
                        <h2>Misi hari bahagia</h2>
                    </div>
                    <div class="vv-mission-grid">
                        @foreach ($events as $event)
                            <article class="vv-mission vv-reveal"><span>Mission
                                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3>{{ $event['name'] }}</h3><time
                                    datetime="{{ $event['date'] }}">{{ \Illuminate\Support\Carbon::parse($event['date'])->translatedFormat('d F Y') }}
                                    · {{ $event['time'] }} WIB</time><strong>{{ $event['location'] }}</strong>
                                <p>{{ $event['address'] }}</p>
                                @if ($event['maps_url'] ?? null)
                                    <a class="vv-button" href="{{ $event['maps_url'] }}" target="_blank"
                                        rel="noopener noreferrer">Buka Peta</a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (data_get($settings, 'story', true) && count($stories))
                <section class="vv-section vv-map" id="map">
                    <div class="vv-section-title vv-reveal"><span>World Map</span>
                        <h2>Jejak perjalanan kami</h2>
                    </div>
                    <div class="vv-route">
                        @foreach ($stories as $index => $story)
                            <article class="vv-route-stop vv-reveal">
                                <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                @if ($storyPhotos->get($index))
                                    <img src="{{ asset('storage/' . $storyPhotos->get($index)->file_path) }}"
                                        alt="{{ $story['title'] }}" width="{{ $storyPhotos->get($index)->width }}"
                                        height="{{ $storyPhotos->get($index)->height }}" loading="lazy"
                                        decoding="async">
                                @endif
                                <div><time
                                        @if (!empty($story['date'])) datetime="{{ $story['date'] }}" @endif>{{ !empty($story['date']) ? \Illuminate\Support\Carbon::parse($story['date'])->translatedFormat('Y') : '' }}</time>
                                    <h3>{{ $story['title'] }}</h3>
                                    <p>{{ $story['story'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if (data_get($settings, 'gallery', true) && $gallery->isNotEmpty())
                <section class="vv-section vv-inventory" id="inventory">
                    <div class="vv-section-title vv-reveal"><span>Inventory</span>
                        <h2>Momen tersimpan</h2>
                    </div>
                    <div class="vv-gallery">
                        @foreach ($gallery as $photo)
                            <figure class="vv-reveal"><img src="{{ asset('storage/' . $photo->file_path) }}"
                                    alt="Momen {{ $invitation->title }} {{ $loop->iteration }}"
                                    width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy"
                                    decoding="async">
                                <figcaption>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($showRsvp || $showGift)
                <section class="vv-section vv-station" id="station" data-vv-section="station">
                    <div class="vv-section-title vv-reveal"><span>Guest Station</span>
                        <h2>Siapkan perjalananmu</h2>
                    </div>
                    <div class="vv-station-grid {{ !($showRsvp && $showGift) ? 'single' : '' }}">
                        @if ($showRsvp)
                            <article class="vv-console vv-reveal">
                                <span class="vv-console-title">RSVP Console</span>
                                <h3>Konfirmasi Kehadiran</h3>
                                <p data-form-status role="status" aria-live="polite"
                                    @if (!session('rsvp_success')) hidden @endif>{{ session('rsvp_success') }}</p>
                                <form method="POST" action="{{ route('public.rsvp', $invitation) }}"
                                    data-async-form="rsvp">@csrf
                                    <label>Nama<input name="guest_name" value="{{ old('guest_name', $guestName) }}"
                                            required><small data-field-error="guest_name"></small></label>
                                    <label>Kehadiran<select id="attendance" name="attendance" required>
                                            <option value="attending">Hadir</option>
                                            <option value="not_attending">Tidak hadir</option>
                                        </select><small data-field-error="attendance"></small></label>
                                    <label data-guest-count>Jumlah Tamu<input id="guest_count" type="number"
                                            name="guest_count" min="1" max="10" value="1"><small
                                            data-field-error="guest_count"></small></label>
                                    <button class="vv-button vv-primary" type="submit">Kirim RSVP</button>
                                </form>
                            </article>
                        @endif
                        @if ($showGift)
                            <article class="vv-console vv-reveal"><span class="vv-console-title">Gift Chest</span>
                                <h3>Wedding Gift</h3>
                                <p>Doa restu adalah hadiah utama. Tanda kasih dapat disampaikan melalui:</p>
                                @foreach ($banks as $bank)
                                    <div class="vv-bank">
                                        <small>{{ $bank['bank_name'] }}</small><strong>{{ $bank['account_number'] }}</strong><span>a.n.
                                            {{ $bank['account_name'] }}</span><button type="button"
                                            data-account="{{ $bank['account_number'] }}">Salin</button></div>
                                @endforeach
                                @if ($qris)
                                    <img class="vv-qris" src="{{ asset('storage/' . $qris->file_path) }}"
                                        alt="QRIS wedding gift" width="{{ $qris->width }}"
                                        height="{{ $qris->height }}" loading="lazy" decoding="async">
                                @endif
                            </article>
                        @endif
                    </div>
                </section>
            @endif

            @if (data_get($settings, 'wishes', true))
                <section class="vv-section vv-messages" id="messages">
                    <div class="vv-section-title vv-reveal"><span>Message Server</span>
                        <h2>Pesan dari para pemain</h2>
                    </div>
                    <div class="vv-message-grid">
                        <div class="vv-chat" data-wish-list>
                            @foreach ($invitation->wishes as $wish)
                                <article class="vv-reveal"><strong>{{ $wish->guest_name }}</strong>
                                    <p>{{ $wish->message }}</p><time
                                        datetime="{{ $wish->created_at->toIso8601String() }}">{{ $wish->created_at->diffForHumans() }}</time>
                                </article>
                            @endforeach
                        </div>
                        @if (!$isPreview)
                            <div class="vv-console vv-reveal"><span class="vv-console-title">Send Message</span>
                                <p data-form-status role="status" aria-live="polite" hidden></p>
                                <form method="POST" action="{{ route('public.wishes', $invitation) }}"
                                    data-async-form="wish">@csrf<label>Nama<input name="guest_name"
                                            value="{{ old('guest_name', $guestName) }}" required><small
                                            data-field-error="guest_name"></small></label><label>Pesan
                                        <textarea name="message" rows="5" required></textarea><small data-field-error="message"></small>
                                    </label><button class="vv-button vv-primary" type="submit">Kirim Pesan</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            <footer class="vv-finish vv-reveal">
                <div class="vv-portal" aria-hidden="true"></div><span>Journey Complete</span>
                <h2>{{ data_get($bride, 'nickname') }} + {{ data_get($groom, 'nickname') }}</h2>
                <p>Terima kasih telah menjadi bagian dari dunia dan perjalanan baru kami.</p><time
                    datetime="{{ $weddingDate->toDateString() }}">{{ $weddingDate->translatedFormat('d F Y') }}</time><small>Built
                    with love · {{ config('app.name') }}</small>
            </footer>
        </main>
    </div>

    <div class="vv-toast" role="status" aria-live="polite"></div>
    @include('templates.partials.youtube-music')
    @if (!$musicVideoId && $musicMedia && data_get($settings, 'music', true))
        <audio id="wedding-music" loop preload="none"
            data-start-seconds="{{ (int) data_get($data, 'music.start_seconds', 0) }}">
            <source src="{{ asset('storage/' . $musicMedia->file_path) }}">
        </audio><button class="music-toggle" type="button" aria-label="Putar atau jeda musik"
            hidden><span></span></button>
    @endif
</body>

</html>
