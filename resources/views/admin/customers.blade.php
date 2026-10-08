@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')
    @include('partials.datatables-assets')
    <section class="dashboard-intro"><div><p class="overline">Pemantauan</p><h1>Pelanggan</h1><p>Pelanggan, undangan, dan transaksi.</p></div></section>
    <section class="panel datatable-panel">
        <div class="table-scroll">
            <table class="premium-table" data-datatable data-source="{{ route('admin.customers.data') }}">
                <thead><tr><th>No.</th><th>Pelanggan</th><th>Email</th><th>Undangan</th><th>Pembayaran</th><th>Terdaftar</th></tr></thead>
                <tbody></tbody>
            </table>
            <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'name', 'name' => 'name'], ['data' => 'email', 'name' => 'email'], ['data' => 'invitations_count', 'name' => 'invitations_count', 'searchable' => false], ['data' => 'payments_count', 'name' => 'payments_count', 'searchable' => false], ['data' => 'created_at', 'name' => 'created_at', 'searchable' => false]], 'order' => [[0, 'desc']]]) !!}</script>
        </div>
    </section>
@endsection
