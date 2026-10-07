@extends('layouts.app')
@section('title', 'Ucapan ' . $invitation->title)
@section('content')<div class="page-head">
        <div>
            <div class="eyebrow">{{ $invitation->title }}</div>
            <h1>Ucapan</h1>
        </div><a class="button secondary" href="{{ route('dashboard') }}">Kembali</a>
    </div>
    <form class="management-filters" method="GET"><input class="input" type="search" name="search"
            value="{{ request('search') }}" placeholder="Cari tamu atau ucapan"><button class="button secondary"
            type="submit">Cari</button></form>
    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tamu</th>
                    <th>Ucapan</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($wishes as $wish)
                    <tr>
                        <td>{{ $wishes->firstItem() + $loop->index }}</td>
                        <td>{{ $wish->guest_name }}</td>
                        <td>{{ $wish->message }}</td>
                        <td>{{ $wish->created_at->format('d M Y H:i') }}</td>
                </tr>@empty<tr>
                        <td colspan="4">Belum ada ucapan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
<div class="pagination">{{ $wishes->links() }}</div>@endsection
