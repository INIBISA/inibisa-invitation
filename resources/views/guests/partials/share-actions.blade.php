<div class="table-actions">
    <button class="button secondary small" type="button" data-copy-link="{{ route('public.invitation', ['slug' => $invitation->slug, 'to' => $guest->name]) }}">Salin Tautan</button>
    @if ($guest->whatsapp)
        <button class="button success small" type="button" data-share-whatsapp="{{ $guest->whatsapp }}" data-guest-name="{{ $guest->name }}" data-couple-name="{{ data_get($invitation->data, 'groom.nickname') }} &amp; {{ data_get($invitation->data, 'bride.nickname') }}" data-personal-link="{{ route('public.invitation', ['slug' => $invitation->slug, 'to' => $guest->name]) }}" data-delivery-endpoint="{{ route('guests.delivery', $guest) }}">WhatsApp</button>
    @endif
</div>
