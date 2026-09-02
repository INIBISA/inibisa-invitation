<section class="hero reveal" id="home" data-nav-section="home">
    <div class="hero-date"><span>{{ $weddingDate->format('d') }}</span><small>{{ $weddingDate->translatedFormat('F') }}<br>{{ $weddingDate->format('Y') }}</small></div>
    <div class="hero-portrait">
        <img class="hero-wreath" src="{{ asset('images/templates/sweet-blossom/floral-wreath.webp') }}" alt="" width="760" height="760" decoding="async" aria-hidden="true">
        @if($coverMedia)
            <img class="hero-photo" src="{{ asset('storage/'.$coverMedia->file_path) }}" alt="{{ $invitation->title }}" width="{{ $coverMedia->width }}" height="{{ $coverMedia->height }}" loading="lazy" decoding="async">
        @else
            <img class="hero-photo" src="{{ asset('images/templates/sweet-blossom/couple-placeholder.svg') }}" alt="" width="960" height="1280" loading="lazy" decoding="async">
        @endif
    </div>
    <p class="kicker">We Are Getting Married</p>
    <h2>{{ data_get($groom, 'nickname') }} <i>&</i> {{ data_get($bride, 'nickname') }}</h2>
    <p class="hero-scroll">Scroll Down <span aria-hidden="true">↓</span></p>
</section>
