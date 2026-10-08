@extends('layouts.app')
@section('title', 'Undangan')
@section('content')
    @include('partials.datatables-assets')
    <section class="dashboard-intro"><div><p class="overline">Administrasi</p><h1>Undangan</h1><p>Pantau seluruh undangan pelanggan.</p></div></section>
    <section class="panel datatable-panel"><div class="table-scroll">
        <table class="premium-table" data-datatable data-source="{{ route('admin.invitations.data') }}">
            <thead><tr><th>No.</th><th>Undangan</th><th>Pelanggan</th><th>Templat</th><th>Status</th><th>Tautan</th></tr></thead><tbody></tbody>
        </table>
        <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'invitation', 'name' => 'invitation', 'orderable' => false], ['data' => 'customer', 'name' => 'customer', 'orderable' => false], ['data' => 'template_name', 'name' => 'template_name', 'orderable' => false], ['data' => 'status_label', 'name' => 'status'], ['data' => 'link', 'name' => 'link', 'orderable' => false, 'searchable' => false]], 'order' => [[0, 'desc']]]) !!}</script>
    </div></section>
@endsection
