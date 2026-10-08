@extends('layouts.app')
@section('title', 'Daftar Tamu')
@section('content')
    @include('partials.datatables-assets')
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

    <section class="panel whatsapp-template-card">
        <div class="panel-header">
            <div><p class="overline">Pesan Global</p><h2>Template Pesan WhatsApp</h2><p>Berlaku untuk semua undangan Anda. Nama tamu dan tautan akan berubah otomatis.</p></div>
            @if(auth()->user()->whatsapp_message_template)
                <form method="POST" action="{{ route('customer.whatsapp-message.destroy') }}" data-confirm="Kembalikan pesan WhatsApp ke template default?">@csrf @method('DELETE')<button class="button secondary small" type="submit">Gunakan Default</button></form>
            @endif
        </div>
        <form method="POST" action="{{ route('customer.whatsapp-message.update') }}">
            @csrf @method('PATCH')
            <div class="field">
                <label for="message_template">Isi pesan</label>
                <textarea class="input whatsapp-template-input" id="message_template" name="message_template" rows="14" maxlength="5000" required data-whatsapp-template-input>{{ old('message_template', $whatsAppMessageTemplate) }}</textarea>
                @error('message_template')<small class="field-error">{{ $message }}</small>@enderror
            </div>
            <div class="whatsapp-placeholder-list" aria-label="Placeholder pesan">
                <span>Masukkan placeholder:</span>
                @foreach (\App\Support\WhatsAppInvitationMessage::PLACEHOLDERS as $placeholder)
                    <button type="button" data-insert-placeholder="{{ $placeholder }}">{{ $placeholder }}</button>
                @endforeach
            </div>
            <small>Format WhatsApp: <code>*tebal*</code> dan <code>_miring_</code>. Maksimal 5.000 karakter.</small>
            <div class="actions"><button class="button gold" type="submit">Simpan Pesan</button></div>
        </form>
    </section>

    <script type="application/json" data-whatsapp-message-template>{!! Illuminate\Support\Js::encode($whatsAppMessageTemplate) !!}</script>

    <section class="panel guest-list-panel">
        <div class="panel-header"><div><p class="overline">Penerima</p><h2>Daftar Tamu</h2><p>Gunakan pencarian dan penyaring untuk mengelola tamu.</p></div></div>
        <form class="guest-filter-grid" id="guest-filters">
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
            <button class="button secondary" type="submit">Saring</button><button class="button secondary" type="button" data-datatable-reset>Atur Ulang</button>
        </form>

        <div class="table-scroll">
            <table class="premium-table guest-table" data-datatable data-source="{{ route('invitations.guests.data', $invitation) }}" data-filter-form="#guest-filters">
                <thead><tr><th>No.</th><th>Tamu</th><th>Status</th><th>Bagikan</th><th>Aksi</th></tr></thead>
                <tbody></tbody>
            </table>
            <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'guest', 'name' => 'name'], ['data' => 'delivery', 'name' => 'sent_at', 'searchable' => false], ['data' => 'share', 'name' => 'share', 'orderable' => false, 'searchable' => false], ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]], 'order' => [[1, 'asc']]]) !!}</script>
        </div>
        <p class="guest-delivery-note">Status “Terkirim” tercatat saat WhatsApp dibuka atau ditandai manual; bukan konfirmasi pesan telah dibaca penerima.</p>
    </section>
@endsection
