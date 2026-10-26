@if(count(data_get($data, 'events', [])))
<section class="section events" id="event" data-nav-section="event">
    <header class="section-head reveal"><p class="kicker">The Celebration</p><h2>A Night to Remember</h2><img class="ocean-divider" src="{{ asset('images/templates/blue-ocean/wave-divider.svg') }}" alt="" width="1080" height="360" loading="lazy" decoding="async" aria-hidden="true"></header>
    <div class="event-line">
        @foreach(data_get($data,'events',[]) as $event)
            <article class="event-card reveal"><span class="event-dot"></span><p class="kicker">{{ $event['name'] }}</p><h3>{{ \Illuminate\Support\Carbon::parse($event['date'])->translatedFormat('d F Y') }}</h3><p class="event-time">{{ $event['time'] }} WIB</p><strong>{{ $event['location'] }}</strong><p>{{ $event['address'] }}</p>@if($event['maps_url'] ?? null)<a class="ocean-button" href="{{ $event['maps_url'] }}" target="_blank" rel="noopener noreferrer">Lihat Lokasi</a>@endif</article>
        @endforeach
    </div>
    <img class="event-accent" src="{{ asset('images/templates/blue-ocean/sea-garden.svg') }}" alt="" width="480" height="480" loading="lazy" decoding="async" aria-hidden="true">
</section>
@endif
