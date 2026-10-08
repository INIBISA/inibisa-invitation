<div class="table-actions">
    <form method="POST" action="{{ route('guests.delivery', $guest) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="sent" value="{{ $guest->sent_at ? 0 : 1 }}">
        <button class="button secondary small" type="submit">{{ $guest->sent_at ? 'Batalkan Terkirim' : 'Tandai Terkirim' }}</button>
    </form>
    <details class="guest-edit">
        <summary class="button secondary small">Ubah</summary>
        <form method="POST" action="{{ route('guests.update', $guest) }}">
            @csrf
            @method('PUT')
            <label>Nama<input class="input" name="name" value="{{ $guest->name }}" maxlength="150" required></label>
            <label>WhatsApp<input class="input" name="whatsapp" type="tel" value="{{ $guest->whatsapp }}"></label>
            <button class="button success small" type="submit">Simpan</button>
        </form>
    </details>
    <form method="POST" action="{{ route('guests.destroy', $guest) }}" data-confirm="Hapus tamu {{ $guest->name }}?">
        @csrf
        @method('DELETE')
        <button class="button danger small" type="submit">Hapus</button>
    </form>
</div>
