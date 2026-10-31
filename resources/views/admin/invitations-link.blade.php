@if ($invitation->status === \App\Models\Invitation::STATUS_PUBLISHED)
    <div class="table-actions">
        <a class="button secondary small" href="{{ route('public.invitation', $invitation->slug) }}" target="_blank" rel="noopener">Buka</a>
        <form method="POST" action="{{ route('admin.invitations.publication.destroy', $invitation) }}" data-confirm="Hapus tautan aktif undangan ini? Tamu tidak dapat membukanya sampai pelanggan mempublikasikan ulang.">
            @csrf
            @method('DELETE')
            <button class="button danger small" type="submit">Hapus Tautan</button>
        </form>
    </div>
@else
    <span class="muted">Belum publik</span>
@endif
