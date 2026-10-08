@extends('layouts.app')
@section('title', 'Konfirmasi Kehadiran')
@section('content')
    @include('partials.datatables-assets')
    <section class="dashboard-intro"><div><p class="overline">Pengelolaan Tamu</p><h1>Konfirmasi Kehadiran</h1><p>Tinjau konfirmasi dari seluruh undangan pelanggan.</p></div></section>
    <section class="metric-grid compact-metrics">
        <article class="metric-card"><div><p>Total Respons</p><strong>{{ number_format($statistics['total']) }}</strong></div></article>
        <article class="metric-card"><div><p>Hadir</p><strong>{{ number_format($statistics['attending']) }}</strong></div></article>
        <article class="metric-card"><div><p>Tidak Hadir</p><strong>{{ number_format($statistics['not_attending']) }}</strong></div></article>
        <article class="metric-card"><div><p>Perkiraan Tamu</p><strong>{{ number_format($statistics['guests']) }}</strong></div></article>
    </section>
    <section class="panel datatable-panel">
        <form class="management-filters" id="admin-rsvp-filters"><select class="input" name="attendance"><option value="">Semua kehadiran</option><option value="attending">Hadir</option><option value="not_attending">Tidak hadir</option></select><button class="button secondary" type="submit">Saring</button><button class="button secondary" type="button" data-datatable-reset>Atur Ulang</button></form>
        <div class="table-scroll">
            <table class="premium-table" data-datatable data-source="{{ route('admin.rsvps.data') }}" data-filter-form="#admin-rsvp-filters">
                <thead><tr><th>No.</th><th>Tamu</th><th>Undangan</th><th>Pelanggan</th><th>Kehadiran</th><th>Jumlah</th><th>Diterima</th></tr></thead><tbody></tbody>
            </table>
            <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'guest_name', 'name' => 'guest_name'], ['data' => 'invitation_title', 'name' => 'invitation_title', 'orderable' => false], ['data' => 'customer_name', 'name' => 'customer_name', 'orderable' => false], ['data' => 'attendance_label', 'name' => 'attendance', 'searchable' => false], ['data' => 'guest_count', 'name' => 'guest_count', 'searchable' => false], ['data' => 'created_at', 'name' => 'created_at', 'searchable' => false]], 'order' => [[0, 'desc']]]) !!}</script>
        </div>
    </section>
@endsection
