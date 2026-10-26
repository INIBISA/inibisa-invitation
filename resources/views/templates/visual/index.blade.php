@php
    $data = $invitation->data;
    $design = $invitation->template->designSettings();
    $media = $invitation->media->groupBy('collection');
    $settings = data_get($data, 'settings', []);
    $musicMedia = $media->get('music')?->first();
    $musicVideoId = data_get($data, 'music.youtube_video_id') ?: ((!$musicMedia && data_get($settings, 'music', true)) ? config('music.default_youtube_video_id') : null);
    $weddingDate = data_get($data, 'wedding_date') ? \Illuminate\Support\Carbon::parse(data_get($data, 'wedding_date')) : null;
    $canonical = $invitation instanceof \App\Models\TemplateDemo ? route('templates.show', $invitation->template) : route('public.invitation', $invitation->slug);
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $invitation->title }} · {{ $invitation->template->name }}</title>
    <meta name="description" content="Undangan pernikahan {{ data_get($data, 'groom.nickname') }} dan {{ data_get($data, 'bride.nickname') }}.">
    <link rel="canonical" href="{{ $canonical }}">
    <link rel="stylesheet" href="{{ asset('css/templates/visual.css') }}?v={{ filemtime(public_path('css/templates/visual.css')) }}">
    @include('templates.partials.design-settings')
    @if ($musicVideoId)<link rel="stylesheet" href="{{ asset('css/youtube-player.css') }}"><script src="{{ asset('js/youtube-player.js') }}" defer></script>@endif
    <script src="{{ asset('js/invitation-forms.js') }}" defer></script>
    @if ($musicMedia && !$musicVideoId)<script src="{{ asset('js/templates/visual.js') }}" defer></script>@endif
</head>
<body class="visual-invitation">
    <div class="visual-shell">
        @include('templates.visual.partials.section', ['section' => 'cover'])
        <main>
            @foreach ($design['section_order'] as $section)
                @if (($section === 'couple' || ($design['sections'][$section] ?? true)) && (!in_array($section, ['countdown', 'story', 'gallery', 'rsvp', 'gift', 'wishes'], true) || data_get($settings, $section, true)))
                    @include('templates.visual.partials.section', ['section' => $section])
                @endif
            @endforeach
        </main>
        @include('templates.visual.partials.section', ['section' => 'closing'])
    </div>
    @include('templates.partials.youtube-music')
    @if ($musicMedia && !$musicVideoId && data_get($settings, 'music', true))<audio id="wedding-music" loop preload="none" data-start-seconds="{{ (int) data_get($data, 'music.start_seconds', 0) }}"><source src="{{ asset('storage/'.$musicMedia->file_path) }}"></audio><button class="visual-music-toggle" type="button" aria-label="Putar musik">♪</button>@endif
</body>
</html>
