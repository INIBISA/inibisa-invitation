<footer class="closing">
    <img class="closing-bouquet" src="{{ asset('images/templates/sweet-blossom/floral-bouquet.webp') }}" alt="" width="960" height="480" loading="lazy" decoding="async" aria-hidden="true">
    <p class="kicker">Thank You</p><p>Merupakan suatu kehormatan dan kebahagiaan bagi kami apabila Anda berkenan hadir dan memberikan doa restu.</p><h2>{{ data_get($groom, 'nickname') }} <i>&</i> {{ data_get($bride, 'nickname') }}</h2><p class="closing-date">{{ $weddingDate->format('d . m . Y') }}</p><img class="floral-divider" src="{{ asset('images/templates/sweet-blossom/floral-divider.webp') }}" alt="" width="1080" height="360" loading="lazy" decoding="async" aria-hidden="true"><small>Made with care · {{ config('app.name') }}</small>
</footer>
