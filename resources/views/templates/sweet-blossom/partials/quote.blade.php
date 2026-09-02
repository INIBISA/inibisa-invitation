@if(data_get($data, 'quote'))
<section class="quote reveal">
    <img class="quote-floral quote-floral-left" src="{{ asset('images/templates/sweet-blossom/side-left.webp') }}" alt="" width="400" height="800" loading="lazy" decoding="async" aria-hidden="true">
    <img class="quote-floral quote-floral-right" src="{{ asset('images/templates/sweet-blossom/side-right.webp') }}" alt="" width="400" height="800" loading="lazy" decoding="async" aria-hidden="true">
    <span class="quote-mark">“</span>
    <blockquote>{{ data_get($data, 'quote') }}</blockquote>
    <img class="floral-divider dark-divider" src="{{ asset('images/templates/sweet-blossom/floral-divider.webp') }}" alt="" width="1080" height="360" loading="lazy" decoding="async" aria-hidden="true">
    <p>Two souls, one beautiful beginning.</p>
</section>
@endif
