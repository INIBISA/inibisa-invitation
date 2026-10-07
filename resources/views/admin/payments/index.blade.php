@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <section class="dashboard-intro">
        <div><p class="overline">Pemantauan</p><h1>Pembayaran</h1><p>Verifikasi transfer manual dan pantau seluruh transaksi.</p></div>
        <span class="admin-page-count">{{ number_format($payments->total()) }} transaksi</span>
    </section>

    <section class="panel management-panel admin-payment-panel">
        <div class="panel-header management-header">
            <div><h2>Daftar Transaksi</h2><p>Gunakan penyaring untuk menemukan pembayaran pelanggan.</p></div>
        </div>
        <form class="admin-payment-filters" method="GET">
            <label class="admin-filter-search">
                <span>Pelanggan</span>
                <input class="input" type="search" name="customer" value="{{ request('customer') }}" placeholder="Cari nama pelanggan">
            </label>
            <label><span>Metode</span><select class="input" name="method">
                <option value="">Semua metode</option>
                <option value="midtrans" @selected(request('method') === 'midtrans')>Midtrans</option>
                <option value="manual_transfer" @selected(request('method') === 'manual_transfer')>Transfer Manual</option>
            </select></label>
            <label><span>Status</span><select class="input" name="status">
                <option value="">Semua status</option>
                @foreach (['pending', 'waiting_approval', 'paid', 'rejected', 'failed', 'expired', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ['pending' => 'Tertunda', 'waiting_approval' => 'Menunggu Persetujuan', 'paid' => 'Lunas', 'rejected' => 'Ditolak', 'failed' => 'Gagal', 'expired' => 'Kedaluwarsa', 'cancelled' => 'Dibatalkan'][$status] }}</option>
                @endforeach
            </select></label>
            <label><span>Dari tanggal</span><input class="input" type="date" name="from" value="{{ request('from') }}"></label>
            <label><span>Sampai tanggal</span><input class="input" type="date" name="to" value="{{ request('to') }}"></label>
            <div class="admin-filter-actions">
                @if (request()->hasAny(['customer', 'method', 'status', 'from', 'to']))
                    <a class="button secondary" href="{{ route('admin.payments.index') }}">Atur Ulang</a>
                @endif
                <button class="button gold" type="submit">Terapkan Penyaring</button>
            </div>
        </form>

        <div class="table-scroll">
            <table class="premium-table admin-payment-table">
                <thead><tr><th>No.</th><th>Pelanggan</th><th>Pesanan</th><th>Metode</th><th>Status</th><th>Tanggal</th><th>Bukti</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $loop->index }}</td>
                            <td><div class="admin-customer-cell"><span>{{ mb_strtoupper(mb_substr($payment->user->name, 0, 1)) }}</span><div><strong>{{ $payment->user->name }}</strong><small>{{ $payment->user->email }}</small></div></div></td>
                            <td><div class="admin-order-cell"><strong>{{ $payment->template->name }}</strong><small>Rp {{ number_format($payment->amount, 0, ',', '.') }}</small></div></td>
                            <td><span class="payment-method-label">{{ $payment->methodLabel() }}</span></td>
                            <td><span class="status-badge {{ $payment->status }}"><i></i>{{ $payment->statusLabel() }}</span></td>
                            <td><span class="admin-date-cell">{{ $payment->created_at->format('d M Y') }}<small>{{ $payment->created_at->format('H:i') }}</small></span></td>
                            <td>
                                @if ($payment->proof_path)
                                    <button class="proof-thumb" type="button" data-image-preview="{{ route('payments.proof.show', $payment) }}" aria-label="Lihat bukti transfer {{ $payment->user->name }}"><img src="{{ route('payments.proof.show', $payment) }}" alt="Bukti transfer" loading="lazy"></button>
                                @else
                                    <span class="muted">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    @if ($payment->payment_method === 'manual_transfer' && $payment->status === 'waiting_approval')
                                        <form method="POST" action="{{ route('admin.payments.update', $payment) }}" data-confirm="Setujui pembayaran ini?">@csrf @method('PATCH')<input type="hidden" name="status" value="paid"><button class="button success small" type="submit">Setujui</button></form>
                                        <button class="button danger small" type="button" data-reject-payment="{{ $payment->id }}">Tolak</button>
                                        <dialog class="app-dialog reject-dialog" data-reject-dialog="{{ $payment->id }}">
                                            <form method="POST" action="{{ route('admin.payments.update', $payment) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="rejected"><h2>Alasan Penolakan</h2><p>Pelanggan akan melihat alasan ini dan dapat mengunggah ulang bukti.</p><textarea class="input" name="rejection_reason" rows="4" maxlength="1000" required placeholder="Jelaskan alasan penolakan"></textarea><div class="actions"><button class="button secondary" type="button" data-dialog-close>Batal</button><button class="button danger" type="submit">Tolak Pembayaran</button></div></form>
                                        </dialog>
                                    @else
                                        <span class="muted">Selesai</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8"><div class="admin-table-empty"><strong>Transaksi tidak ditemukan</strong><span>Ubah filter atau tunggu pembayaran baru.</span></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $payments->links() }}</div>
    </section>
    <dialog class="app-dialog image-dialog" data-image-dialog><button class="icon-button dialog-close" type="button" data-dialog-close aria-label="Tutup">×</button><img data-image-target alt="Pratinjau bukti transfer"></dialog>
@endsection
