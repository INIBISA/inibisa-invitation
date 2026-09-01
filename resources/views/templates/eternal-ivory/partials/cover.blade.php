<section class="opening" aria-labelledby="opening-title">
    @if($coverMedia)
        <img class="opening-image" src="{{ asset('storage/'.$coverMedia->file_path) }}" alt="{{ $invitation->title }}" width="{{ $coverMedia->width }}" height="{{ $coverMedia->height }}" fetchpriority="high" decoding="async">
    @else
        <img class="opening-image" src="{{ asset('images/templates/eternal-ivory/couple-placeholder.svg') }}" alt="" width="960" height="1280" fetchpriority="high" decoding="async">
    @endif
    <div class="opening-shade"></div>
    <img class="floral-corner floral-corner-left" src="{{ asset('images/templates/eternal-ivory/floral-corner-left.webp') }}" alt="" width="720" height="720" decoding="async" aria-hidden="true">
    <img class="floral-corner floral-corner-right" src="{{ asset('images/templates/eternal-ivory/floral-corner-right.webp') }}" alt="" width="720" height="720" decoding="async" aria-hidden="true">
    <div class="opening-frame">
        <p class="kicker">The Wedding Of</p>
        <h1 id="opening-title">{{ data_get($groom, 'nickname') }}<i>&</i>{{ data_get($bride, 'nickname') }}</h1>
        <p class="cover-date">{{ $weddingDate->translatedFormat('l, d F Y') }}</p>
        <div class="guest"><small>Kepada Yth.<br>Bapak/Ibu/Saudara/i</small><strong>{{ $guestName !== '' ? $guestName : 'Tamu Undangan' }}</strong></div>
        <button class="open-button" id="open-invitation" type="button">Buka Undangan</button>
        <span class="scroll-cue" aria-hidden="true">⌄</span>
    </div>
</section>
