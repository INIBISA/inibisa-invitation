@extends('layouts.app')
@section('title', 'Daftar Tamu')
@section('content')
    <section class="dashboard-intro guest-page-intro">
        <div>
            <p class="overline">{{ $invitation->title }}</p>
            <h1>Daftar Tamu</h1>
            <p>Kelola penerima, tautan personal, dan status pengiriman undangan.</p>
        </div>
        <div class="actions">
            <button class="button secondary" type="button" data-copy-link="{{ route('public.invitation', $invitation->slug) }}">Salin Tautan Umum</button>
            <a class="button secondary" href="{{ route('invitations.edit', $invitation) }}">Kembali ke Undangan</a>
        </div>
    </section>

    @php($sentGuests = (int) ($guestStats->sent ?? 0))
    @php($totalGuests = (int) ($guestStats->total ?? 0))
    <section class="guest-stat-grid" aria-label="Ringkasan tamu">
        <article><span>Total Tamu</span><strong>{{ number_format($totalGuests) }}</strong></article>
        <article><span>Sudah Dikirim</span><strong>{{ number_format($sentGuests) }}</strong></article>
        <article><span>Belum Dikirim</span><strong>{{ number_format($totalGuests - $sentGuests) }}</strong></article>
    </section>

    <div class="guest-entry-grid">
        <section class="panel guest-entry-card">
            <div class="guest-card-heading"><div><p class="overline">Satu Tamu</p><h2>Tambah Tamu</h2></div><span>01</span></div>
            <form method="POST" action="{{ route('invitations.guests.store', $invitation) }}">
                @csrf
                <div class="field"><label for="guest-name">Nama tamu</label><input class="input" id="guest-name" name="name" value="{{ old('name') }}" maxlength="150" required></div>
                <div class="field"><label for="guest-whatsapp">Nomor WhatsApp <small>Opsional</small></label><input class="input" id="guest-whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}" placeholder="6281234567890"></div>
                <button class="button gold" type="submit">Tambah Tamu</button>
            </form>
        </section>

        <section class="panel guest-entry-card guest-import-card">
            <div class="guest-card-heading"><div><p class="overline">Banyak Tamu</p><h2>Import Excel</h2></div><span>02</span></div>
            <p>Gunakan kolom <strong>nama</strong> dan <strong>whatsapp</strong>. Nomor WhatsApp duplikat otomatis dilewati.</p>
            <form method="POST" action="{{ route('invitations.guests.import', $invitation) }}" enctype="multipart/form-data">
                @csrf
                <div class="field"><label for="guest-file">File Excel atau CSV</label><input class="input guest-file-input" id="guest-file" name="guest_file" type="file" accept=".xlsx,.xls,.csv" data-upload-ready="true" required><small>Maksimal 2 MB dan 1.000 baris.</small></div>
                <div class="actions"><button class="button gold" type="submit">Import Tamu</button><a class="button secondary" href="{{ route('invitations.guests.template', $invitation) }}">Unduh Template</a></div>
            </form>
        </section>
    </div>

    <section class="panel guest-list-panel">
        <div class="panel-header">
            <div><p class="overline">Penerima</p><h2>Daftar Tamu</h2><p>{{ $guests->total() }} hasil ditemukan</p></div>
        </div>
        <form method="GET" class="guest-filter-grid">
            <input class="input" type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama tamu">
            <select class="input" name="contact">
                <option value="">Semua kontak</option>
                <option value="with_whatsapp" @selected(request('contact') === 'with_whatsapp')>Dengan WhatsApp</option>
                <option value="without_whatsapp" @selected(request('contact') === 'without_whatsapp')>Tanpa WhatsApp</option>
            </select>
            <select class="input" name="delivery">
                <option value="">Semua status</option>
                <option value="sent" @selected(request('delivery') === 'sent')>Sudah dikirim</option>
                <option value="unsent" @selected(request('delivery') === 'unsent')>Belum dikirim</option>
            </select>
            <button class="button secondary" type="submit">Saring</button>
            @if(request()->hasAny(['search', 'contact', 'delivery']))<a class="button secondary" href="{{ route('invitations.guests.index', $invitation) }}">Atur Ulang</a>@endif
        </form>

        <div class="table-scroll">
            <table class="premium-table guest-table">
                <thead><tr><th>No.</th><th>Tamu</th><th>Status</th><th>Bagikan</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr>
                            <td>{{ $guests->firstItem() + $loop->index }}</td>
                            <td><div class="guest-person"><span>{{ mb_strtoupper(mb_substr($guest->name, 0, 1)) }}</span><div><strong>{{ $guest->name }}</strong><small>{{ $guest->whatsapp ?: 'Tanpa WhatsApp' }}</small></div></div></td>
                            <td><span class="status-badge {{ $guest->sent_at ? 'published' : 'draft' }}"><i></i>{{ $guest->sent_at ? 'Terkirim' : 'Belum dikirim' }}</span>@if($guest->sent_at)<small class="guest-sent-time">{{ $guest->sent_at->format('d M Y, H:i') }}</small>@endif</td>
                            <td><div class="table-actions">
                                <button class="button secondary small" type="button" data-copy-link="{{ route('public.invitation', ['slug' => $invitation->slug, 'to' => $guest->name]) }}">Salin Tautan</button>
                                @if($guest->whatsapp)<button class="button success small" type="button" data-share-whatsapp="{{ $guest->whatsapp }}" data-personal-link="{{ route('public.invitation', ['slug' => $invitation->slug, 'to' => $guest->name]) }}" data-delivery-endpoint="{{ route('guests.delivery', $guest) }}">WhatsApp</button>@endif
                            </div></td>
                            <td><div class="table-actions">
                                <form method="POST" action="{{ route('guests.delivery', $guest) }}">@csrf @method('PATCH')<input type="hidden" name="sent" value="{{ $guest->sent_at ? 0 : 1 }}"><button class="button secondary small" type="submit">{{ $guest->sent_at ? 'Batalkan Terkirim' : 'Tandai Terkirim' }}</button></form>
                                <details class="guest-edit"><summary class="button secondary small">Ubah</summary><form method="POST" action="{{ route('guests.update', $guest) }}">@csrf @method('PUT')<label>Nama<input class="input" name="name" value="{{ $guest->name }}" maxlength="150" required></label><label>WhatsApp<input class="input" name="whatsapp" type="tel" value="{{ $guest->whatsapp }}"></label><button class="button success small" type="submit">Simpan</button></form></details>
                                <form method="POST" action="{{ route('guests.destroy', $guest) }}" data-confirm="Hapus tamu {{ $guest->name }}?">@csrf @method('DELETE')<button class="button danger small" type="submit">Hapus</button></form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="guest-empty"><strong>Belum ada tamu yang cocok</strong><span>Tambah tamu baru atau ubah filter pencarian.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="guest-delivery-note">Status “Terkirim” tercatat saat WhatsApp dibuka atau ditandai manual; bukan konfirmasi pesan telah dibaca penerima.</p>
        <div class="pagination">{{ $guests->links() }}</div>
    </section>
@endsection
