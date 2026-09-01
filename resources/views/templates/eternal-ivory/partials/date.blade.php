@if(data_get($settings, 'countdown', true))
<section class="date-panel reveal">
    <img class="date-floral" src="{{ asset('images/templates/eternal-ivory/floral-bouquet.webp') }}" alt="" width="960" height="480" loading="lazy" decoding="async" aria-hidden="true">
    <p class="kicker">Save The Date</p><h2>Save<br>The<br>Date</h2><strong class="date-number">{{ $weddingDate->format('d') }}</strong><p class="date-month">{{ $weddingDate->translatedFormat('F Y') }}</p>
    <div class="countdown" data-countdown="{{ $weddingDate->startOfDay()->toIso8601String() }}"><div><strong data-days>00</strong><span>Hari</span></div><div><strong data-hours>00</strong><span>Jam</span></div><div><strong data-minutes>00</strong><span>Menit</span></div><div><strong data-seconds>00</strong><span>Detik</span></div></div>
</section>
@endif
