@if ($musicVideoId && data_get($settings, 'music', true))
    <div class="youtube-music" data-youtube-music="{{ $musicVideoId }}" data-youtube-start="{{ (int) data_get($data, 'music.start_seconds', 0) }}">
        <button class="youtube-music-toggle" type="button" data-youtube-toggle aria-label="Putar musik" hidden><span aria-hidden="true">♪</span><span class="sr-only" data-youtube-label>Putar Musik</span></button>
        <div class="youtube-music-frame" data-youtube-frame aria-hidden="true"></div>
    </div>
@endif
