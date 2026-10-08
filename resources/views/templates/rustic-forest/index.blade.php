@php
    $data = $invitation->data;
    $groom = data_get($data, 'groom', []);
    $bride = data_get($data, 'bride', []);
    $settings = data_get($data, 'settings', []);
    $weddingDate = \Illuminate\Support\Carbon::parse(data_get($data, 'wedding_date'));
    $media = $invitation->media->groupBy('collection');
    $coverMedia = $media->get('cover')?->first();
    $musicMedia = $media->get('music')?->first();
    $canonical = route('public.invitation', $invitation->slug);
@endphp
<!doctype html>
<html lang="id" class="invitation-locked">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>{{ $invitation->title }} - {{ $weddingDate->translatedFormat('d F Y') }}</title>
    <meta name="description"
        content="Undangan pernikahan {{ data_get($groom, 'nickname') }} dan {{ data_get($bride, 'nickname') }} pada {{ $weddingDate->translatedFormat('d F Y') }}.">
    <meta name="theme-color" content="#EFE7D8">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="The Wedding of {{ data_get($groom, 'nickname') }} & {{ data_get($bride, 'nickname') }}">
    <meta property="og:description" content="{{ $weddingDate->translatedFormat('d F Y') }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($coverMedia)
        <meta property="og:image" content="{{ asset('storage/' . $coverMedia->file_path) }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/templates/rustic-forest.css') }}">
    <link rel="stylesheet" href="{{ asset('css/templates/eternal-ivory-animations.css') }}">
    <script src="{{ asset('js/templates/eternal-ivory.js') }}" defer></script>
</head>

<body>
    <div class="invitation-wrapper">
        @include('templates.eternal-ivory.partials.cover')
        <main id="invitation-content">
            @include('templates.eternal-ivory.partials.hero')
            @include('templates.eternal-ivory.partials.quote')
            @include('templates.eternal-ivory.partials.couple')
            @include('templates.eternal-ivory.partials.date')
            @include('templates.eternal-ivory.partials.events')
            @include('templates.eternal-ivory.partials.story')
            @include('templates.eternal-ivory.partials.gallery')
            @include('templates.eternal-ivory.partials.rsvp')
            @include('templates.eternal-ivory.partials.gift')
            @include('templates.eternal-ivory.partials.wishes')
            @include('templates.eternal-ivory.partials.closing')
        </main>
        @include('templates.eternal-ivory.partials.bottom-navigation')
    </div>
    @if ($musicMedia && data_get($settings, 'music', true))
        <audio id="wedding-music" loop preload="none">
            <source src="{{ asset('storage/' . $musicMedia->file_path) }}">
        </audio><button class="music-toggle" type="button" aria-label="Putar atau jeda musik"
            hidden><span></span></button>
    @endif
</body>

</html>
