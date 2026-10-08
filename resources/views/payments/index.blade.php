@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Transaksi</p>
            <h1>Pembayaran</h1>
            <p>Status seluruh pembayaran Anda.</p>
        </div><a class="primary-button" href="{{ route('templates.index') }}">Pilih Templat</a>
    </section>
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
                            <td colspan="7">Belum ada pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $payments->links() }}
    </section>
@endsection
