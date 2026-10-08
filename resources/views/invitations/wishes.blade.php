@extends('layouts.app')
@section('title', 'Ucapan ' . $invitation->title)
@section('content')
    @include('partials.datatables-assets')
    <div class="page-head"><div><div class="eyebrow">{{ $invitation->title }}</div><h1>Ucapan</h1></div><a class="button secondary" href="{{ route('dashboard') }}">Kembali</a></div>
    <section class="card table-wrap datatable-panel"><div class="table-scroll">
        <table class="table" data-datatable data-source="{{ route('invitations.wishes.data', $invitation) }}">
            <thead><tr><th>No.</th><th>Tamu</th><th>Ucapan</th><th>Waktu</th></tr></thead><tbody></tbody>
        </table>
        <script type="application/json" data-datatable-config>{!! Illuminate\Support\Js::encode(['columns' => [['data' => 'DT_RowIndex', 'name' => 'id', 'searchable' => false], ['data' => 'guest_name', 'name' => 'guest_name'], ['data' => 'message', 'name' => 'message'], ['data' => 'created_at', 'name' => 'created_at', 'searchable' => false]], 'order' => [[0, 'desc']]]) !!}</script>
    </div></section>
@endsection
