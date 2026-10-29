@extends('layouts.app')
@section('title', 'Dasbor')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Ruang Kerja</p>
            <h1>Selamat datang, {{ auth()->user()->name }}</h1>
            <p>Semua undangan, pembayaran, tamu, RSVP, dan ucapan Anda dikelola dari sini.</p>
        </div><a class="primary-button" href="{{ route('templates.index') }}">Pilih Templat</a>
    </section>
    <section class="customer-guide" aria-label="Panduan memulai">
        <h2>Langkah membuat undangan</h2>
        <p>Pendaftaran akun gratis. Harga tiap template terlihat sebelum checkout; pengisian undangan terbuka setelah pembayaran berhasil.</p>
        <ol><li><a href="{{ route('templates.index') }}">Lihat demo dan pilih template</a></li><li>Bayar template pilihan</li><li>Isi dan pratinjau undangan</li><li>Publikasikan, bagikan, lalu pantau tamu</li></ol>
    </section>
    @if ($availablePayments->isNotEmpty())
        <section class="panel dashboard-callout">
            <div><span class="status-badge paid"><i></i>Pembayaran berhasil</span>
                <h2>Templat Anda siap diisi</h2>
                <p>Isi detail acara, simpan draf, lalu pratinjau sebelum mempublikasikan undangan.</p>
            </div>
            <div class="actions">
                @foreach ($availablePayments as $payment)
                    <a class="button success"
                        href="{{ route('invitations.create', ['template_id' => $payment->template_id]) }}">Mulai Isi
                        {{ $payment->template->name }}</a>
                @endforeach
            </div>
        </section>
    @endif
    @foreach ($payments->where('status', 'waiting_approval') as $payment)
        <div class="alert success">Pembayaran {{ $payment->template->name }} sedang diverifikasi admin. <a
                href="{{ route('payments.show', $payment) }}">Lihat detail</a></div>
    @endforeach
    @foreach ($payments->where('status', 'rejected') as $payment)
        <div class="alert error">Pembayaran {{ $payment->template->name }} ditolak: {{ $payment->rejection_reason }} <a
                 href="{{ route('payments.show', $payment) }}">Unggah ulang bukti</a></div>
    @endforeach
    @foreach ($payments->where('status', 'pending') as $payment)
        <div class="alert">Pembayaran {{ $payment->template->name }} menunggu penyelesaian. <a
                href="{{ route('payments.show', $payment) }}">Lanjutkan pembayaran</a></div>
    @endforeach
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Undangan Anda</h2>
                <p>Kelola draf, tamu, RSVP, dan ucapan dari satu tempat.</p>
            </div>
        </div>
        @if ($invitations->isEmpty())
            <div class="premium-empty dashboard-empty"><span class="empty-symbol">✦</span>
                <h3>Belum punya undangan</h3>
                 <p>Pilih template dan lihat harganya. Setelah pembayaran berhasil, Anda bisa mulai mengisi undangan.</p><a class="button gold"
                    href="{{ route('templates.index') }}">Lihat Templat</a>
        </div>@else<div class="table-scroll">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Undangan</th>
                            <th>Templat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invitations as $invitation)
                            <tr>
                                <td>{{ $invitations->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $invitation->title }}</strong><br><span
                                        class="muted">{{ $invitation->slug }}</span></td>
                                <td>{{ $invitation->template->name }}</td>
                                <td><span
                                        class="status-badge {{ $invitation->status }}"><i></i>{{ $invitation->statusLabel() }}</span>
                                </td>
                                <td>
                                    <div class="table-actions"><a class="button secondary small"
                                            href="{{ route('invitations.edit', $invitation) }}">Ubah</a><a
                                            class="button secondary small"
                                            href="{{ route('invitations.guests.index', $invitation) }}">Tamu</a><a
                                            class="button secondary small"
                                            href="{{ route('invitations.rsvps', $invitation) }}">RSVP</a><a
                                            class="button secondary small"
                                            href="{{ route('invitations.wishes', $invitation) }}">Ucapan</a></div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $invitations->links() }}</div>
        @endif
    </section>
@endsection
