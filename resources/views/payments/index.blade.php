@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Transaksi</p>
            <h1>Pembayaran</h1>
            <p>Lihat status transaksi, lanjutkan pembayaran tertunda, atau unggah ulang bukti yang ditolak.</p>
        </div><a class="primary-button" href="{{ route('templates.index') }}">Pilih Templat</a>
    </section>
    <section class="customer-guide" aria-label="Arti status pembayaran"><h2>Memahami status pembayaran</h2><p><strong>Menunggu pembayaran:</strong> buka Detail untuk menyelesaikan transaksi. <strong>Menunggu verifikasi:</strong> admin sedang memeriksa bukti transfer. <strong>Ditolak:</strong> lihat alasan dan unggah bukti baru. <strong>Berhasil:</strong> template siap digunakan untuk membuat undangan.</p></section>
    <section class="panel">
        <div class="table-scroll">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Templat</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $loop->index }}</td>
                            <td>{{ $payment->template->name }}</td>
                            <td>{{ $payment->methodLabel() }}</td>
                            <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                            <td><span
                                    class="status-badge {{ $payment->status }}"><i></i>{{ $payment->statusLabel() }}</span>
                            </td>
                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                            <td><a class="button secondary small" href="{{ route('payments.show', $payment) }}">Detail</a>
                            </td>
                    </tr>@empty<tr>
                             <td colspan="7">Belum ada pembayaran. <a href="{{ route('templates.index') }}">Lihat demo dan harga template</a> untuk memulai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $payments->links() }}
    </section>
@endsection
