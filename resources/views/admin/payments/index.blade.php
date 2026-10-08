@extends('layouts.app')
@section('title', 'Pembayaran')
@section('content')
    @include('partials.datatables-assets')
    <section class="dashboard-intro"><div><p class="overline">Pemantauan</p><h1>Pembayaran</h1><p>Verifikasi transfer manual dan pantau seluruh transaksi.</p></div><span class="admin-page-count">{{ number_format($paymentCount) }} transaksi</span></section>
    <section class="panel management-panel admin-payment-panel datatable-panel">
        <form class="admin-payment-filters" id="admin-payment-filters">
            <label><span>Pelanggan</span><input class="input" type="search" name="customer" placeholder="Cari nama pelanggan"></label>
            <label><span>Metode</span><select class="input" name="method"><option value="">Semua metode</option><option value="midtrans">Midtrans</option><option value="manual_transfer">Transfer Manual</option></select></label>
            <label><span>Status</span><select class="input" name="status"><option value="">Semua status</option>@foreach(['pending' => 'Tertunda', 'waiting_approval' => 'Menunggu Persetujuan', 'paid' => 'Lunas', 'rejected' => 'Ditolak', 'failed' => 'Gagal', 'expired' => 'Kedaluwarsa', 'cancelled' => 'Dibatalkan'] as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
            <label><span>Dari tanggal</span><input class="input" type="date" name="from"></label><label><span>Sampai tanggal</span><input class="input" type="date" name="to"></label>
            <div class="admin-filter-actions"><button class="button secondary" type="button" data-datatable-reset>Atur Ulang</button><button class="button gold" type="submit">Terapkan</button></div>
        </form>
        <div class="table-scroll">
            <table class="premium-table admin-payment-table" data-datatable data-source="{{ route('admin.payments.data') }}" data-filter-form="#admin-payment-filters">
                <thead><tr><th>No.</th><th>Pelanggan</th><th>Pesanan</th><th>Metode</th><th>Status</th><th>Tanggal</th><th>Bukti</th><th>Aksi</th></tr></thead><tbody></tbody>
            </table>
            <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'customer', 'name' => 'customer', 'orderable' => false], ['data' => 'order', 'name' => 'order', 'orderable' => false], ['data' => 'method_label', 'name' => 'payment_method'], ['data' => 'status_label', 'name' => 'status'], ['data' => 'date', 'name' => 'created_at', 'searchable' => false], ['data' => 'proof', 'name' => 'proof', 'orderable' => false, 'searchable' => false], ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]], 'order' => [[0, 'desc']]]) !!}</script>
        </div>
    </section>
    <dialog class="app-dialog image-dialog" data-image-dialog><button class="icon-button dialog-close" type="button" data-dialog-close aria-label="Tutup">×</button><img data-image-target alt="Pratinjau bukti transfer"></dialog>
@endsection
