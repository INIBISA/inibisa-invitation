@extends('layouts.app')
@section('title', 'Dasbor Admin')
@section('content')
    <section class="dashboard-intro admin-dashboard-intro">
        <div>
            <p class="overline">Ringkasan</p>
            <h1>Dasbor Admin</h1>
            <p>Ringkasan aktivitas pelanggan, pembayaran, dan undangan.</p>
        </div>
        <a class="primary-button" href="{{ route('admin.payments.index', ['status' => 'waiting_approval']) }}">Tinjau Transfer</a>
    </section>

    @if ($statistics['waiting_approval'] > 0)
        <a class="admin-review-callout" href="{{ route('admin.payments.index', ['status' => 'waiting_approval']) }}">
            <span class="admin-review-icon">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v18M3 12h18" /></svg>
            </span>
            <span><strong>{{ number_format($statistics['waiting_approval']) }} transfer menunggu verifikasi</strong><small>Tinjau bukti pembayaran pelanggan sekarang.</small></span>
            <b>Tinjau</b>
        </a>
    @endif

    <section class="admin-metric-grid" aria-label="Statistik utama">
        @foreach ([
            ['Pelanggan', 'customers', 'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8'],
            ['Total Transaksi', 'payments', 'M2 6h20v13H2zM2 10h20M6 15h5'],
            ['Total Undangan', 'invitations', 'M3 5h18v14H3zM3 7l9 7 9-7'],
            ['Total Tamu', 'guests', 'M4 20v-2a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v2M12 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8'],
        ] as [$label, $key, $icon])
            <article class="admin-metric-card">
                <span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $icon }}" /></svg></span>
                <div><p>{{ $label }}</p><strong>{{ number_format($statistics[$key]) }}</strong></div>
            </article>
        @endforeach
    </section>

    <section class="admin-secondary-stats" aria-label="Statistik tambahan">
        @foreach ([
            ['Transaksi Hari Ini', 'payments_today'],
            ['Pembayaran Tertunda', 'pending_payments'],
            ['Dipublikasikan', 'published_invitations'],
            ['RSVP', 'rsvps'],
            ['Ucapan', 'wishes'],
        ] as [$label, $key])
            <article><span>{{ $label }}</span><strong>{{ number_format($statistics[$key]) }}</strong></article>
        @endforeach
    </section>

    <section class="panel admin-recent-panel">
        <div class="panel-header">
            <div><p class="overline">Aktivitas Terbaru</p><h2>Undangan Terbaru</h2><p>Undangan terbaru dari seluruh pelanggan.</p></div>
            <a class="button secondary small" href="{{ route('admin.invitations.index') }}">Lihat Semua</a>
        </div>
        <div class="table-scroll">
            <table class="premium-table">
                <thead><tr><th>No.</th><th>Pelanggan</th><th>Undangan</th><th>Templat</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentInvitations as $invitation)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $invitation->user->name }}</strong></td>
                            <td>{{ $invitation->title }}</td>
                            <td>{{ $invitation->template->name }}</td>
                            <td><span class="status-badge {{ $invitation->status }}"><i></i>{{ $invitation->statusLabel() }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="admin-table-empty"><strong>Belum ada undangan</strong><span>Undangan pelanggan terbaru akan tampil di sini.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
