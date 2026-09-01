@if(!$isPreview && data_get($settings, 'rsvp', true))
<section class="section response reveal" id="rsvp" data-nav-section="rsvp">
    <img class="response-floral" src="{{ asset('images/templates/eternal-ivory/floral-corner-right.webp') }}" alt="" width="720" height="720" loading="lazy" decoding="async" aria-hidden="true">
    <header class="section-head"><p class="kicker">Répondez S'il Vous Plaît</p><h2>Will You<br>Be There?</h2><p>We would be delighted to celebrate this beautiful moment with you.</p></header>
    @if(session('rsvp_success'))<p class="form-message">{{ session('rsvp_success') }}</p>@endif
    <form method="POST" action="{{ route('public.rsvp', $invitation) }}">@csrf<div class="ivory-field"><label for="rsvp_name">Nama</label><input id="rsvp_name" name="guest_name" value="{{ old('guest_name', $guestName) }}" placeholder="Ketik nama Anda" required></div><div class="ivory-field"><label for="attendance">Kehadiran</label><select id="attendance" name="attendance" required><option value="attending">Hadir</option><option value="not_attending">Tidak hadir</option></select></div><div class="ivory-field"><label for="guest_count">Jumlah tamu</label><input id="guest_count" type="number" name="guest_count" min="1" max="10" value="1"></div><button class="ivory-button dark" type="submit">Kirim RSVP</button></form>
</section>
@endif
