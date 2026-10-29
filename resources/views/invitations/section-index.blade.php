@extends('layouts.app')
@section('title', $title)
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Kelola Undangan</p>
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>
    <section class="customer-guide" aria-label="Panduan {{ $title }}">
        <h2>{{ $title }} untuk apa?</h2>
        @if ($title === 'Tamu Undangan')
            <p>Tambahkan tamu pada undangan yang dipilih untuk membuat tautan pribadi dan membagikannya lewat WhatsApp. Publikasikan undangan sebelum membagikan tautan.</p>
        @elseif ($title === 'RSVP')
            <p>RSVP adalah jawaban hadir atau tidak hadir yang dikirim tamu melalui undangan yang sudah dipublikasikan. Pilih undangan untuk melihat jawabannya.</p>
        @else
            <p>Ucapan adalah pesan yang ditulis tamu melalui halaman undangan. Pilih undangan untuk membaca pesan yang masuk.</p>
        @endif
    </section>
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Pilih Undangan</h2>
                <p>Pilih undangan yang ingin dikelola.</p>
            </div>
        </div>
        @if ($invitations->total() === 0)
            <div class="premium-empty dashboard-empty">
                <span class="empty-symbol">✦</span>
                <h3>Belum punya undangan</h3>
                <p>Belum ada undangan untuk dikelola. Pilih template, selesaikan pembayaran, lalu buat undangan terlebih dahulu.</p>
                <a class="button gold" href="{{ route('templates.index') }}">Pilih Templat</a>
            </div>
        @else
            <div class="table-scroll">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Undangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invitations as $invitation)
                            <tr>
                                <td><strong>{{ $invitation->title }}</strong><br><span class="muted">{{ $invitation->slug }}</span></td>
                                <td><span class="status-badge {{ $invitation->status }}"><i></i>{{ $invitation->statusLabel() }}</span></td>
                                <td><a class="button secondary small" href="{{ route($invitationRoute, $invitation) }}">Lihat {{ $title }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $invitations->links() }}</div>
        @endif
    </section>
@endsection
