@php
    $data = $invitation->data;
    $groom = data_get($data, 'groom', []);
    $bride = data_get($data, 'bride', []);
    $settings = data_get($data, 'settings', []);
    $weddingDate = \Illuminate\Support\Carbon::parse(data_get($data, 'wedding_date'));
    $media = $invitation->media->groupBy('collection');
    $coverMedia = $media->get('cover')?->first();
    $musicMedia = $media->get('music')?->first();
    $musicVideoId = data_get($data, 'music.youtube_video_id')
        ?: ((!$musicMedia && data_get($settings, 'music', true)) ? config('music.default_youtube_video_id') : null);
    $canonical =
        $invitation instanceof \App\Models\TemplateDemo
            ? route('templates.show', $invitation->template)
            : route('public.invitation', $invitation->slug);
    $templateDesign = $invitation->template->designSettings();
    $templateSections = $templateDesign['sections'];
    $sectionOrder = $templateDesign['section_order'];
    $sectionPartials = ['quote' => 'quote', 'couple' => 'couple', 'countdown' => 'date', 'events' => 'events', 'story' => 'story', 'gallery' => 'gallery', 'rsvp' => 'rsvp', 'gift' => 'gift', 'wishes' => 'wishes'];
    $templateStylesheet = match ($invitation->template->key) {
        'rustic-forest' => 'css/templates/rustic-forest.css',
        'modern-minimalist' => 'css/templates/modern-minimalist.css',
        default => 'css/templates/eternal-ivory.css',
    };
@endphp
<!doctype html>
<html lang="id" class="invitation-locked">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <title>{{ $invitation->title }} · {{ $weddingDate->translatedFormat('d F Y') }}</title>
    <meta name="description"
        content="Undangan pernikahan {{ data_get($groom, 'nickname') }} dan {{ data_get($bride, 'nickname') }} pada {{ $weddingDate->translatedFormat('d F Y') }}.">
    <meta name="theme-color" content="{{ $templateDesign['background_color'] }}">
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:type" content="website">
    <meta property="og:title"
        content="The Wedding of {{ data_get($groom, 'nickname') }} & {{ data_get($bride, 'nickname') }}">
    <meta property="og:description" content="{{ $weddingDate->translatedFormat('d F Y') }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($coverMedia)
        <meta property="og:image" content="{{ asset('storage/' . $coverMedia->file_path) }}">
    @endif
    <link rel="stylesheet" href="{{ asset($templateStylesheet) }}">
    <link rel="stylesheet" href="{{ asset('css/templates/eternal-ivory-animations.css') }}">
    @include('templates.partials.design-settings')
    @if ($musicVideoId)
        <link rel="stylesheet" href="{{ asset('css/youtube-player.css') }}">
        <script src="{{ asset('js/youtube-player.js') }}" defer></script>
    @endif
    <script src="{{ asset('js/templates/eternal-ivory.js') }}" defer></script>
    <script src="{{ asset('js/invitation-forms.js') }}" defer></script>
</head>

<body>
    <div class="invitation-wrapper">
        @include('templates.eternal-ivory.partials.cover')
        <main id="invitation-content">
            @include('templates.eternal-ivory.partials.hero')
            @foreach ($sectionOrder as $section)
                @if ($section === 'couple' || $templateSections[$section])
                    @include('templates.eternal-ivory.partials.'.$sectionPartials[$section])
                @endif
            @endforeach
            @include('templates.eternal-ivory.partials.closing')
        </main>
        @include('templates.eternal-ivory.partials.bottom-navigation')
    </div>
    @include('templates.partials.youtube-music')
    @if (!$musicVideoId && $musicMedia && data_get($settings, 'music', true))
        <audio id="wedding-music" loop preload="none" data-start-seconds="{{ (int) data_get($data, 'music.start_seconds', 0) }}">
            <source src="{{ asset('storage/' . $musicMedia->file_path) }}">
        </audio><button class="music-toggle" type="button" aria-label="Putar atau jeda musik"
            hidden><span></span></button>
    @endif
</body>

</html>
