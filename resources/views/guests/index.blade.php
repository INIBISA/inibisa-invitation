@extends('layouts.app')
@section('title', 'Daftar Tamu')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">{{ $invitation->title }}</p>
            <h1>Daftar Tamu</h1>
            <p>Kelola penerima undangan dan bagikan link personal.</p>
        </div><a class="button secondary" href="{{ route('invitations.edit', $invitation) }}">Kembali ke Undangan</a>
    </section>
    <section class="panel form-section">
        <h2>Tambah Tamu</h2>
        <form method="POST" action="{{ route('invitations.guests.store', $invitation) }}">@csrf<div class="form-grid">
                <div class="field"><label for="guest-name">Nama tamu</label><input class="input" id="guest-name"
                        name="name" value="{{ old('name') }}" required></div>
                <div class="field"><label for="guest-whatsapp">Nomor WhatsApp (opsional)</label><input class="input"
                        id="guest-whatsapp" name="whatsapp" type="tel" value="{{ old('whatsapp') }}"
                        placeholder="6281234567890"></div>
            </div><button class="button gold" type="submit">Tambah Tamu</button></form>
    </section>
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Daftar Tamu</h2>
                <p>{{ $guests->total() }} tamu tercatat</p>
            </div>
            <div class="actions"><button class="button secondary small" type="button"
                    data-copy-link="{{ route('public.invitation', $invitation->slug) }}">Salin Tautan Umum</button></div>
        </div>
        <form method="GET" class="management-filters guest-filters"><input class="input" type="search" name="search"
                value="{{ request('search') }}" placeholder="Cari nama tamu"><select class="input" name="contact">
                <option value="">Semua tamu</option>
                <option value="with_whatsapp" @selected(request('contact') === 'with_whatsapp')>Dengan WhatsApp</option>
                <option value="without_whatsapp" @selected(request('contact') === 'without_whatsapp')>Tanpa WhatsApp</option>
            </select><button class="button secondary" type="submit">Saring</button></form>
        <div class="table-scroll">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>WhatsApp</th>
                        <th>Tautan Pribadi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr>
                            <td>{{ $guests->firstItem() + $loop->index }}</td>
                            <td><strong>{{ $guest->name }}</strong></td>
                            <td>{{ $guest->whatsapp ?: '—' }}</td>
                            <td>
                                <div class="table-actions"><button class="button secondary small" type="button"
                                        data-copy-link="{{ route('public.invitation', ['slug' => $invitation->slug, 'to' => $guest->name]) }}">Copy
                                        Tautan Pribadi</button>
                                    @if ($guest->whatsapp)
                                        <button class="button success small" type="button"
                                            data-share-whatsapp="{{ $guest->whatsapp }}"
                                            data-personal-link="{{ route('public.invitation', ['slug' => $invitation->slug, 'to' => $guest->name]) }}">Share
                                            WhatsApp</button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <details class="guest-edit">
                                        <summary class="button secondary small">Ubah</summary>
                                        <form method="POST" action="{{ route('guests.update', $guest) }}">@csrf
                                            @method('PUT')<label>Nama<input class="input" name="name"
                                                    value="{{ $guest->name }}" required></label><label>WhatsApp<input
                                                    class="input" name="whatsapp" type="tel"
                                                    value="{{ $guest->whatsapp }}"></label><button
                                                class="button success small" type="submit">Simpan</button></form>
                                    </details>
                                    <form method="POST" action="{{ route('guests.destroy', $guest) }}"
                                        data-confirm="Hapus tamu {{ $guest->name }}?">@csrf @method('DELETE')<button
                                            class="button danger small" type="submit">Hapus</button></form>
                                </div>
                            </td>
                    </tr>@empty<tr>
                            <td colspan="5">Belum ada tamu yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $guests->links() }}</div>
    </section>
@endsection
