@php($groomPhoto = $media->get('groom')?->first())
@php($bridePhoto = $media->get('bride')?->first())
<section class="section couple" id="couple" data-nav-section="couple">
    <img class="section-sprig section-sprig-left" src="{{ asset('images/templates/eternal-ivory/floral-sprig-left.webp') }}" alt="" width="400" height="800" loading="lazy" decoding="async" aria-hidden="true">
    <header class="section-head reveal">
        <p class="kicker">Bride & Groom</p><h2>Dengan penuh kebahagiaan</h2>
        <img class="floral-divider" src="{{ asset('images/templates/eternal-ivory/floral-divider.webp') }}" alt="" width="1080" height="360" loading="lazy" decoding="async" aria-hidden="true">
        <p>kami mengundang Anda untuk menjadi bagian dari hari istimewa kami.</p>
    </header>
    @foreach([[$bride,$bridePhoto,'The Bride'],[$groom,$groomPhoto,'The Groom']] as [$person,$photo,$label])
        <article class="person reveal {{ $loop->even ? 'person-reverse' : '' }}">
            <div class="portrait">
                @if($photo)
                    <img src="{{ asset('storage/'.$photo->file_path) }}" alt="{{ data_get($person, 'full_name') }}" width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy" decoding="async">
                @else
                    <img src="{{ asset('images/templates/eternal-ivory/couple-placeholder.svg') }}" alt="" width="960" height="1280" loading="lazy" decoding="async">
                @endif
            </div>
            <div class="person-copy"><p class="kicker">{{ $label }}</p><h3>{{ data_get($person, 'full_name') }}</h3><p>Putra/Putri dari<br>{{ data_get($person, 'father') }} & {{ data_get($person, 'mother') }}</p>@if(data_get($person,'instagram'))<span class="social">{{ data_get($person,'instagram') }}</span>@endif</div>
        </article>
    @endforeach
    <img class="section-sprig section-sprig-right" src="{{ asset('images/templates/eternal-ivory/floral-sprig-right.webp') }}" alt="" width="400" height="800" loading="lazy" decoding="async" aria-hidden="true">
</section>
