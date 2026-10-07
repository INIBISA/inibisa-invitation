@if ($musicVideoId && data_get($settings, 'music', true))
    <div class="youtube-music" data-youtube-music="{{ $musicVideoId }}">
        <button class="youtube-music-toggle" type="button" data-youtube-toggle aria-label="Jeda musik" hidden>♪</button>
        <div class="youtube-music-frame" data-youtube-frame aria-hidden="true"></div>
    </div>
@endif
