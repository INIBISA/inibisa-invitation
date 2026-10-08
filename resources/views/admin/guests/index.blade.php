@extends('layouts.app')
@section('title', 'Tamu')
@section('content')
    @include('partials.datatables-assets')
    <section class="dashboard-intro"><div><p class="overline">Pemantauan</p><h1>Daftar Tamu</h1><p>Tinjau penerima dari seluruh undangan.</p></div></section>
    <section class="panel datatable-panel">
        <form class="management-filters" id="admin-guest-filters"><input class="input" name="customer" placeholder="Pelanggan"><input class="input" name="invitation" placeholder="Undangan"><button class="button secondary" type="submit">Saring</button><button class="button secondary" type="button" data-datatable-reset>Atur Ulang</button></form>
        <div class="table-scroll">
            <table class="premium-table" data-datatable data-source="{{ route('admin.guests.data') }}" data-filter-form="#admin-guest-filters">
                <thead><tr><th>No.</th><th>Tamu</th><th>WhatsApp</th><th>Status Kirim</th><th>Undangan</th><th>Pelanggan</th></tr></thead><tbody></tbody>
            </table>
            <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'name', 'name' => 'name'], ['data' => 'whatsapp', 'name' => 'whatsapp', 'defaultContent' => '—'], ['data' => 'delivery', 'name' => 'sent_at', 'searchable' => false], ['data' => 'invitation_title', 'name' => 'invitation_title', 'orderable' => false], ['data' => 'customer_name', 'name' => 'customer_name', 'orderable' => false]], 'order' => [[1, 'asc']]]) !!}</script>
        </div>
    </section>
@endsection
