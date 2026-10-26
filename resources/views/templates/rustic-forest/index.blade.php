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
    $musicVideoId = data_get($data, 'music.youtube_video_id') ?: ((! $musicMedia && data_get($settings, 'music', true)) ? config('music.default_youtube_video_id') : null);
    $canonical = $invitation instanceof \App\Models\TemplateDemo ? route('templates.show', $invitation->template) : route('public.invitation', $invitation->slug);
    $templateDesign = $invitation->template->designSettings();
    $templateSections = $templateDesign['sections'];
    $sectionOrder = $templateDesign['section_order'];
    $visible = [
        'quote' => $templateSections['quote'] && filled(data_get($data, 'quote')),
        'couple' => true,
        'countdown' => $templateSections['countdown'] && data_get($settings, 'countdown', true),
        'events' => $templateSections['events'] && count($events),
        'story' => $templateSections['story'] && data_get($settings, 'story', true) && count($stories),
        'gallery' => $templateSections['gallery'] && data_get($settings, 'gallery', true) && $gallery->isNotEmpty(),
        'rsvp' => $templateSections['rsvp'] && ! $isPreview && data_get($settings, 'rsvp', true),
        'gift' => $templateSections['gift'] && data_get($settings, 'gift', true) && (count($banks) || $qris),
        'wishes' => $templateSections['wishes'] && data_get($settings, 'wishes', true),
    ];
@endphp
<!doctype html>
<html lang="id" class="invitation-locked">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>{{ $invitation->title }} · {{ $weddingDate->translatedFormat('d F Y') }}</title>
    <meta name="description" content="Undangan pernikahan {{ data_get($groom, 'nickname') }} dan {{ data_get($bride, 'nickname') }} pada {{ $weddingDate->translatedFormat('d F Y') }}.">
    <meta name="theme-color" content="{{ $templateDesign['background_color'] }}"><link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}"><link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website"><meta property="og:title" content="The Wedding of {{ data_get($bride, 'nickname') }} & {{ data_get($groom, 'nickname') }}"><meta property="og:url" content="{{ $canonical }}">
    @if($coverMedia)<meta property="og:image" content="{{ asset('storage/'.$coverMedia->file_path) }}">@endif
    <link rel="stylesheet" href="{{ asset('css/templates/rustic-forest.css') }}">@include('templates.partials.design-settings')
    @if($musicVideoId)<link rel="stylesheet" href="{{ asset('css/youtube-player.css') }}"><script src="{{ asset('js/youtube-player.js') }}" defer></script>@endif
    <script src="{{ asset('js/templates/editorial-invitation.js') }}" defer></script><script src="{{ asset('js/invitation-forms.js') }}" defer></script>
</head>
<body class="rf-body" data-editorial-invitation>
    <section class="rf-gate" data-invitation-gate aria-labelledby="rf-gate-title">
        @if($coverMedia)<img src="{{ asset('storage/'.$coverMedia->file_path) }}" alt="" width="{{ $coverMedia->width }}" height="{{ $coverMedia->height }}" fetchpriority="high">@endif
        <div class="rf-gate-shade"></div><div class="rf-gate-branches" aria-hidden="true"></div>
        <div class="rf-gate-copy"><span>Undangan Pernikahan</span><h1 id="rf-gate-title">{{ data_get($bride, 'nickname') }}<i>&</i>{{ data_get($groom, 'nickname') }}</h1><time>{{ $weddingDate->translatedFormat('d F Y') }}</time><p>Kepada Yth.<strong>{{ $guestName ?: 'Tamu Undangan' }}</strong></p><button type="button" data-open-invitation>Buka Undangan</button></div>
    </section>
    <div class="rf-page">
        <header class="rf-masthead" id="home" data-nav-section="home"><span>Est. {{ $weddingDate->format('Y') }}</span><p>A celebration rooted in love</p><strong>RF · 04</strong></header>
        <main>
            <section class="rf-hero"><div><p class="rf-kicker">Save our date</p><h2>{{ data_get($bride, 'nickname') }} <em>&</em><br>{{ data_get($groom, 'nickname') }}</h2><p>Kami mengundang Anda untuk hadir, berkumpul, dan menjadi bagian dari awal perjalanan kami.</p></div><figure>@if($coverMedia)<img src="{{ asset('storage/'.$coverMedia->file_path) }}" alt="{{ $invitation->title }}" width="{{ $coverMedia->width }}" height="{{ $coverMedia->height }}">@else<span>R · N</span>@endif<figcaption>{{ $weddingDate->translatedFormat('l, d F Y') }}</figcaption></figure></section>
            @foreach($sectionOrder as $section)
                @continue(! $visible[$section])
                @if($section === 'quote')<section class="rf-quote rf-reveal"><span aria-hidden="true">“</span><blockquote>{{ data_get($data, 'quote') }}</blockquote><small>Kolose 3:14</small></section>@endif
                @if($section === 'couple')<section class="rf-section rf-couple" id="couple" data-nav-section="couple"><header><p class="rf-kicker">Two souls, one trail</p><h2>Yang Berbahagia</h2></header><div class="rf-couple-grid">@foreach([[$bride,$bridePhoto,'Mempelai Wanita'],[$groom,$groomPhoto,'Mempelai Pria']] as [$person,$photo,$label])<article class="rf-reveal"><figure>@if($photo)<img src="{{ asset('storage/'.$photo->file_path) }}" alt="{{ data_get($person,'full_name') }}" width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy">@else<span>{{ mb_substr(data_get($person,'nickname','?'),0,1) }}</span>@endif</figure><small>{{ $label }}</small><h3>{{ data_get($person,'full_name') }}</h3><p>Putra/Putri dari<br><strong>{{ data_get($person,'father') }}</strong><br>& {{ data_get($person,'mother') }}</p>@if(data_get($person,'instagram'))<em>{{ data_get($person,'instagram') }}</em>@endif</article>@endforeach</div></section>@endif
                @if($section === 'countdown')<section class="rf-countdown rf-reveal"><div><p class="rf-kicker">Counting the days</p><h2>{{ $weddingDate->format('d') }}<span>{{ strtoupper($weddingDate->translatedFormat('M')) }}</span></h2></div><div class="rf-countdown-grid" data-countdown="{{ $weddingDate->copy()->startOfDay()->toIso8601String() }}">@foreach([['days','Hari'],['hours','Jam'],['minutes','Menit'],['seconds','Detik']] as [$unit,$label])<p><strong data-{{ $unit }}>00</strong><span>{{ $label }}</span></p>@endforeach</div></section>@endif
                @if($section === 'events')<section class="rf-section rf-events" id="event" data-nav-section="event"><header><p class="rf-kicker">Gather with us</p><h2>Rangkaian Acara</h2></header><div class="rf-event-list">@foreach($events as $event)<article class="rf-reveal"><span>{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span><div><small>{{ $event['name'] }}</small><h3>{{ \Illuminate\Support\Carbon::parse($event['date'])->translatedFormat('l, d F Y') }}</h3><time>{{ $event['time'] }} WIB</time></div><div><strong>{{ $event['location'] }}</strong><p>{{ $event['address'] }}</p>@if($event['maps_url'] ?? null)<a href="{{ $event['maps_url'] }}" target="_blank" rel="noopener noreferrer">Buka peta</a>@endif</div></article>@endforeach</div></section>@endif
                @if($section === 'story')<section class="rf-section rf-story"><header><p class="rf-kicker">Along the way</p><h2>Jejak Cerita</h2></header><div class="rf-story-list">@foreach($stories as $index=>$story)<article class="rf-reveal"><span>{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span>@if($storyPhotos->get($index))<img src="{{ asset('storage/'.$storyPhotos->get($index)->file_path) }}" alt="{{ $story['title'] }}" width="{{ $storyPhotos->get($index)->width }}" height="{{ $storyPhotos->get($index)->height }}" loading="lazy">@endif<div><time>{{ !empty($story['date']) ? \Illuminate\Support\Carbon::parse($story['date'])->translatedFormat('Y') : '' }}</time><h3>{{ $story['title'] }}</h3><p>{{ $story['story'] }}</p></div></article>@endforeach</div></section>@endif
                @if($section === 'gallery')<section class="rf-section rf-gallery"><header><p class="rf-kicker">Collected moments</p><h2>Galeri Kenangan</h2></header><div class="rf-gallery-grid" data-gallery>@foreach($gallery as $photo)<figure class="rf-reveal"><img src="{{ asset('storage/'.$photo->file_path) }}" alt="Momen {{ $invitation->title }} {{ $loop->iteration }}" width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy"><figcaption>No. {{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</figcaption></figure>@endforeach</div></section>@endif
                @if($section === 'rsvp')<section class="rf-section rf-rsvp" id="rsvp" data-nav-section="rsvp"><div><p class="rf-kicker">Kindly respond</p><h2>Apakah Anda akan hadir?</h2><p>Kabar kehadiran Anda membantu kami menyiapkan perayaan dengan hangat.</p></div><div><p data-form-status role="status" hidden></p><form method="POST" action="{{ route('public.rsvp',$invitation) }}" data-async-form="rsvp">@csrf<label>Nama<input name="guest_name" value="{{ old('guest_name',$guestName) }}" required><small data-field-error="guest_name"></small></label><label>Kehadiran<select id="attendance" name="attendance" required><option value="attending">Hadir</option><option value="not_attending">Tidak hadir</option></select><small data-field-error="attendance"></small></label><label data-guest-count>Jumlah Tamu<input id="guest_count" type="number" name="guest_count" min="1" max="10" value="1"><small data-field-error="guest_count"></small></label><button type="submit">Kirim RSVP</button></form></div></section>@endif
                @if($section === 'gift')<section class="rf-section rf-gift"><header><p class="rf-kicker">A token of love</p><h2>Wedding Gift</h2><p>Doa restu Anda adalah hadiah terindah. Tanda kasih dapat disampaikan melalui:</p></header><div class="rf-bank-grid">@foreach($banks as $bank)<article><small>{{ $bank['bank_name'] }}</small><strong>{{ $bank['account_number'] }}</strong><span>a.n. {{ $bank['account_name'] }}</span><button type="button" data-account="{{ $bank['account_number'] }}">Salin nomor</button></article>@endforeach @if($qris)<img src="{{ asset('storage/'.$qris->file_path) }}" alt="QRIS wedding gift" width="{{ $qris->width }}" height="{{ $qris->height }}" loading="lazy">@endif</div></section>@endif
                @if($section === 'wishes')<section class="rf-section rf-wishes"><header><p class="rf-kicker">Leave a blessing</p><h2>Doa & Ucapan</h2></header><div class="rf-wish-layout"><div class="rf-wish-list" data-wish-list>@foreach($invitation->wishes as $wish)<article><strong>{{ $wish->guest_name }}</strong><p>{{ $wish->message }}</p><time>{{ $wish->created_at->diffForHumans() }}</time></article>@endforeach</div>@if(!$isPreview)<div><p data-form-status role="status" hidden></p><form method="POST" action="{{ route('public.wishes',$invitation) }}" data-async-form="wish">@csrf<label>Nama<input name="guest_name" value="{{ old('guest_name',$guestName) }}" required><small data-field-error="guest_name"></small></label><label>Pesan<textarea name="message" rows="5" required></textarea><small data-field-error="message"></small></label><button type="submit">Kirim Ucapan</button></form></div>@endif</div></section>@endif
            @endforeach
            <footer class="rf-closing"><span aria-hidden="true">❧</span><p>Terima kasih telah menjadi bagian dari perjalanan kami.</p><h2>{{ data_get($bride,'nickname') }} & {{ data_get($groom,'nickname') }}</h2><time>{{ $weddingDate->translatedFormat('d F Y') }}</time></footer>
        </main>
        <nav class="rf-nav" aria-label="Navigasi undangan"><a href="#home" data-nav-link="home" class="active">Home</a>@foreach($sectionOrder as $section)@if($section === 'couple')<a href="#couple" data-nav-link="couple">Pasangan</a>@endif @if($section === 'events' && $visible['events'])<a href="#event" data-nav-link="event">Acara</a>@endif @if($section === 'rsvp' && $visible['rsvp'])<a href="#rsvp" data-nav-link="rsvp">RSVP</a>@endif @endforeach</nav>
    </div>
    @include('templates.partials.youtube-music')
    @if(!$musicVideoId && $musicMedia && data_get($settings,'music',true))<audio id="wedding-music" loop preload="none" data-start-seconds="{{ (int)data_get($data,'music.start_seconds',0) }}"><source src="{{ asset('storage/'.$musicMedia->file_path) }}"></audio><button class="music-toggle" type="button" aria-label="Putar musik" hidden>♪</button>@endif
</body>
</html>
