@extends('layouts.app')
@section('title', 'Tamu')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Pemantauan</p>
            <h1>Daftar Tamu</h1>
        </div>
    </section>
    <section class="panel">
        <form class="management-filters" method="GET"><input name="customer" value="{{ request('customer') }}"
                placeholder="Pelanggan"><input name="invitation" value="{{ request('invitation') }}"
                placeholder="Undangan"><button>Saring</button></form>
        <div class="table-scroll">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tamu</th>
                        <th>WhatsApp</th>
                        <th>Undangan</th>
                        <th>Pelanggan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guests as $guest)
                        <tr>
                            <td>{{ $guests->firstItem() + $loop->index }}</td>
                            <td>{{ $guest->name }}</td>
                            <td>{{ $guest->whatsapp }}</td>
                            <td>{{ $guest->invitation->title }}</td>
                            <td>{{ $guest->invitation->user->name }}</td>
                    </tr>@empty<tr>
                            <td colspan="5">Belum ada tamu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $guests->links() }}
    </section>
@endsection
