@php($storyPhotos = $media->get('story', collect())->values())
@if(data_get($settings, 'story', true) && count(data_get($data, 'stories', [])))
<section class="section story">
    <header class="section-head reveal"><p class="kicker">Our Journey</p><h2>Love Story</h2><img class="floral-divider" src="{{ asset('images/templates/sweet-blossom/floral-divider.webp') }}" alt="" width="1080" height="360" loading="lazy" decoding="async" aria-hidden="true"></header>
    <div class="timeline">
        @foreach(data_get($data,'stories',[]) as $index=>$story)
            <article class="story-item reveal"><span>{{ str_pad((string)($index+1),2,'0',STR_PAD_LEFT) }}</span><div>@if($storyPhotos->get($index))<img class="story-photo" src="{{ asset('storage/'.$storyPhotos->get($index)->file_path) }}" alt="{{ $story['title'] }}" width="{{ $storyPhotos->get($index)->width }}" height="{{ $storyPhotos->get($index)->height }}" loading="lazy" decoding="async">@endif<p class="kicker">{{ !empty($story['date']) ? \Illuminate\Support\Carbon::parse($story['date'])->translatedFormat('Y') : '' }}</p><h3>{{ $story['title'] }}</h3><p>{{ $story['story'] }}</p></div></article>
        @endforeach
    </div>
</section>
@endif
