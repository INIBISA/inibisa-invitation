@extends('layouts.app')
@section('title', 'RSVP ' . $invitation->title)
@section('content')
    @include('partials.datatables-assets')
    <div class="page-head"><div><div class="eyebrow">{{ $invitation->title }}</div><h1>RSVP</h1></div><a class="button secondary" href="{{ route('dashboard') }}">Kembali</a></div>
    <section class="card table-wrap datatable-panel">
        <form class="management-filters" id="rsvp-filters"><select class="input" name="attendance"><option value="">Semua kehadiran</option><option value="attending">Hadir</option><option value="not_attending">Tidak hadir</option></select><button class="button secondary" type="submit">Saring</button><button class="button secondary" type="button" data-datatable-reset>Atur Ulang</button></form>
        <div class="table-scroll">
            <table class="table" data-datatable data-source="{{ route('invitations.rsvps.data', $invitation) }}" data-filter-form="#rsvp-filters">
                <thead><tr><th>No.</th><th>Tamu</th><th>Kehadiran</th><th>Jumlah</th><th>Waktu</th></tr></thead><tbody></tbody>
            </table>
            <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'guest_name', 'name' => 'guest_name'], ['data' => 'attendance_label', 'name' => 'attendance', 'searchable' => false], ['data' => 'guest_count', 'name' => 'guest_count', 'searchable' => false], ['data' => 'created_at', 'name' => 'created_at', 'searchable' => false]], 'order' => [[0, 'desc']]]) !!}</script>
        </div>
    </section>
@endsection
