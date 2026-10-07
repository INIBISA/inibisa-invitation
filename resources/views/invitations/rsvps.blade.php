@extends('layouts.app')
@section('title', 'RSVP ' . $invitation->title)
@section('content')<div class="page-head">
        <div>
            <div class="eyebrow">{{ $invitation->title }}</div>
            <h1>RSVP</h1>
        </div><a class="button secondary" href="{{ route('dashboard') }}">Kembali</a>
    </div>
    <form class="management-filters" method="GET"><input class="input" type="search" name="search"
            value="{{ request('search') }}" placeholder="Cari tamu"><select class="input" name="attendance">
            <option value="">Semua kehadiran</option>
            <option value="attending" @selected(request('attendance') === 'attending')>Hadir</option>
            <option value="not_attending" @selected(request('attendance') === 'not_attending')>Tidak hadir</option>
        </select><button class="button secondary" type="submit">Saring</button></form>
    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tamu</th>
                    <th>Kehadiran</th>
                    <th>Jumlah</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rsvps as $rsvp)
                    <tr>
                        <td>{{ $rsvps->firstItem() + $loop->index }}</td>
                        <td>{{ $rsvp->guest_name }}</td>
                        <td>{{ $rsvp->attendance === 'attending' ? 'Hadir' : 'Tidak hadir' }}</td>
                        <td>{{ $rsvp->guest_count }}</td>
                        <td>{{ $rsvp->created_at->format('d M Y H:i') }}</td>
                </tr>@empty<tr>
                        <td colspan="5">Belum ada RSVP.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
<div class="pagination">{{ $rsvps->links() }}</div>@endsection
