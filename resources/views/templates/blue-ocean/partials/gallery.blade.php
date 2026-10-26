@php($gallery = $media->get('gallery', collect()))
@if(data_get($settings, 'gallery', true) && $gallery->isNotEmpty())
<section class="section gallery"><header class="section-head reveal"><p class="kicker">Glowing Memories</p><h2>Moments in Moonlight</h2><img class="ocean-divider" src="{{ asset('images/templates/blue-ocean/wave-divider.svg') }}" alt="" width="1080" height="360" loading="lazy" decoding="async" aria-hidden="true"></header><div class="gallery-grid">@foreach($gallery as $photo)<figure class="reveal"><img src="{{ asset('storage/'.$photo->file_path) }}" alt="Momen {{ $invitation->title }}" width="{{ $photo->width }}" height="{{ $photo->height }}" loading="lazy" decoding="async"></figure>@endforeach</div></section>
@endif
