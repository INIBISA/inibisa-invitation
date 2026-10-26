@if(!$isPreview && data_get($settings, 'rsvp', true))
<section class="section response reveal" id="rsvp" data-nav-section="rsvp">
    <img class="response-ocean" src="{{ asset('images/templates/blue-ocean/corner-right.svg') }}" alt="" width="720" height="720" loading="lazy" decoding="async" aria-hidden="true">
    <header class="section-head"><p class="kicker">Répondez S'il Vous Plaît</p><h2>Join Us<br>Under The Stars</h2><p>Kehadiran dan doa Anda akan membuat malam kami kian bermakna.</p></header>
    <p class="form-message" data-form-status role="status" aria-live="polite" @if(!session('rsvp_success')) hidden @endif>{{ session('rsvp_success') }}</p>
    <form method="POST" action="{{ route('public.rsvp', $invitation) }}" data-async-form="rsvp">@csrf<div class="ocean-field"><label for="rsvp_name">Nama</label><input id="rsvp_name" name="guest_name" value="{{ old('guest_name', $guestName) }}" placeholder="Ketik nama Anda" required><small data-field-error="guest_name"></small></div><div class="ocean-field"><label for="attendance">Kehadiran</label><select id="attendance" name="attendance" required><option value="attending">Hadir</option><option value="not_attending">Tidak hadir</option></select><small data-field-error="attendance"></small></div><div class="ocean-field"><label for="guest_count">Jumlah tamu</label><input id="guest_count" type="number" name="guest_count" min="1" max="10" value="1"><small data-field-error="guest_count"></small></div><button class="ocean-button dark" type="submit">Kirim RSVP</button></form>
</section>
@endif
