@php
    $canvas = $design['canvas'][$section];
    $settings = data_get($data, 'settings', []);
@endphp
<section class="visual-section visual-{{ $section }}" id="{{ $section }}" style="--visual-section-bg: {{ $canvas['background'] }}; --visual-height: {{ $canvas['height'] }}px; --content-left: {{ $canvas['content_x'] }}%; --content-top: {{ $canvas['content_top'] }}px; --content-width: {{ $canvas['content_width'] }}%; --content-align: {{ $canvas['content_align'] }}">
    <div class="visual-content">
        @switch($section)
            @case('cover')
                <p class="visual-eyebrow">The Wedding Of</p>
                <h1>{{ data_get($data, 'groom.nickname') }} <span>&amp;</span> {{ data_get($data, 'bride.nickname') }}</h1>
                <p>{{ $weddingDate?->translatedFormat('d F Y') }}</p>
                <p class="visual-guest">Kepada Yth. {{ $guestName }}</p>
                <a class="visual-button" href="#couple">Buka Undangan</a>
                @break
            @case('quote')
                <p class="visual-eyebrow">Sebuah Kisah</p>
                <h2>Merayakan Cinta</h2>
                <blockquote>{{ data_get($data, 'quote') }}</blockquote>
                @break
            @case('couple')
                <p class="visual-eyebrow">Kedua Mempelai</p>
                <h2>{{ data_get($data, 'groom.nickname') }} <span>&amp;</span> {{ data_get($data, 'bride.nickname') }}</h2>
                <div class="visual-two-columns">
                    <article><h3>{{ data_get($data, 'groom.full_name') }}</h3><p>Putra dari {{ data_get($data, 'groom.father') }} dan {{ data_get($data, 'groom.mother') }}</p></article>
                    <article><h3>{{ data_get($data, 'bride.full_name') }}</h3><p>Putri dari {{ data_get($data, 'bride.father') }} dan {{ data_get($data, 'bride.mother') }}</p></article>
                </div>
                @break
            @case('countdown')
                @if (data_get($settings, 'countdown', true))
                    <p class="visual-eyebrow">Save the Date</p><h2>Hari Bahagia Kami</h2>
                    <p class="visual-date">{{ $weddingDate?->translatedFormat('l, d F Y') }}</p>
                @endif
                @break
            @case('events')
                <p class="visual-eyebrow">Rangkaian Acara</p><h2>Acara Pernikahan</h2>
                <div class="visual-cards">
                    @foreach (data_get($data, 'events', []) as $event)
                        <article><h3>{{ $event['name'] ?? '' }}</h3><p>{{ !empty($event['date']) ? \Illuminate\Support\Carbon::parse($event['date'])->translatedFormat('d F Y') : '' }} · {{ $event['time'] ?? '' }}</p><p>{{ $event['location'] ?? '' }}<br>{{ $event['address'] ?? '' }}</p>@if (!empty($event['maps_url']) && in_array(parse_url($event['maps_url'], PHP_URL_SCHEME), ['http', 'https'], true))<a href="{{ $event['maps_url'] }}" target="_blank" rel="noopener noreferrer">Lihat lokasi</a>@endif</article>
                    @endforeach
                </div>
                @break
            @case('story')
                @if (data_get($settings, 'story', true))
                    <p class="visual-eyebrow">Perjalanan Kami</p><h2>Cerita Cinta</h2>
                    <div class="visual-cards">@foreach (data_get($data, 'stories', []) as $story)<article><h3>{{ $story['title'] ?? '' }}</h3><p>{{ $story['date'] ?? '' }}</p><p>{{ $story['story'] ?? '' }}</p></article>@endforeach</div>
                @endif
                @break
            @case('gallery')
                @if (data_get($settings, 'gallery', true))
                    <p class="visual-eyebrow">Kenangan</p><h2>Galeri Kami</h2>
                    <div class="visual-gallery">@foreach ($media->get('gallery', collect()) as $photo)<img src="{{ asset('storage/'.$photo->file_path) }}" alt="Kenangan {{ $loop->iteration }}" loading="lazy">@endforeach</div>
                @endif
                @break
            @case('rsvp')
                @if (!$isPreview && data_get($settings, 'rsvp', true))
                    <p class="visual-eyebrow">Konfirmasi Kehadiran</p><h2>RSVP</h2>
                    <p data-form-status role="status" hidden></p>
                    <form class="visual-form" method="POST" action="{{ route('public.rsvp', $invitation) }}" data-async-form="rsvp">@csrf
                        <label>Nama<input name="guest_name" value="{{ $guestName }}" required><small data-field-error="guest_name"></small></label>
                        <label>Kehadiran<select name="attendance" required><option value="attending">Hadir</option><option value="not_attending">Tidak hadir</option></select><small data-field-error="attendance"></small></label>
                        <label>Jumlah tamu<input type="number" name="guest_count" min="1" max="10" value="1"><small data-field-error="guest_count"></small></label>
                        <button class="visual-button" type="submit">Kirim RSVP</button>
                    </form>
                @else
                    <p class="visual-eyebrow">Konfirmasi Kehadiran</p><h2>RSVP</h2><p>Formulir RSVP tersedia pada undangan yang diterbitkan.</p>
                @endif
                @break
            @case('gift')
                @if (data_get($settings, 'gift', true))
                    <p class="visual-eyebrow">Tanda Kasih</p><h2>Wedding Gift</h2>
                    <div class="visual-cards">@foreach (data_get($data, 'banks', []) as $bank)<article><h3>{{ $bank['bank_name'] ?? '' }}</h3><p>{{ $bank['account_number'] ?? '' }}</p><p>a.n. {{ $bank['account_name'] ?? '' }}</p></article>@endforeach</div>
                    @if ($media->get('qris')?->first())<img class="visual-qris" src="{{ asset('storage/'.$media->get('qris')->first()->file_path) }}" alt="QRIS hadiah">@endif
                @endif
                @break
            @case('wishes')
                @if (data_get($settings, 'wishes', true))
                    <p class="visual-eyebrow">Doa dan Harapan</p><h2>Ucapan</h2>
                    @unless ($isPreview)
                        <p data-form-status role="status" hidden></p>
                        <form class="visual-form" method="POST" action="{{ route('public.wishes', $invitation) }}" data-async-form="wish">@csrf<label>Nama<input name="guest_name" value="{{ $guestName }}" required><small data-field-error="guest_name"></small></label><label>Ucapan<textarea name="message" required></textarea><small data-field-error="message"></small></label><button class="visual-button" type="submit">Kirim Ucapan</button></form>
                    @endunless
                    <div class="visual-cards" data-wish-list>@foreach ($invitation->wishes as $wish)<article><h3>{{ $wish->guest_name }}</h3><p>{{ $wish->message }}</p></article>@endforeach</div>
                @endif
                @break
            @case('closing')
                <p class="visual-eyebrow">Terima Kasih</p><h2>{{ data_get($data, 'groom.nickname') }} <span>&amp;</span> {{ data_get($data, 'bride.nickname') }}</h2><p>Kehadiran dan doa restu Anda sangat berarti bagi kami.</p>
                @break
        @endswitch
    </div>
    <div class="visual-layers" aria-hidden="true">
        @foreach ($canvas['elements'] as $element)
            <div class="visual-layer visual-layer-{{ $element['kind'] }} visual-shape-{{ $element['shape'] ?? 'rectangle' }}" style="left: {{ $element['x'] }}%; top: {{ $element['y'] }}%; width: {{ $element['width'] }}%; height: {{ $element['height'] }}%; color: {{ $element['color'] }}; opacity: {{ $element['opacity'] }}; transform: rotate({{ $element['rotation'] }}deg); --layer-font-size: {{ $element['font_size'] }}px">
                @if ($element['kind'] === 'text'){{ $element['content'] ?? '' }}@elseif ($element['kind'] === 'image')<img src="{{ asset($element['image']) }}" alt="">@endif
            </div>
        @endforeach
    </div>
</section>
