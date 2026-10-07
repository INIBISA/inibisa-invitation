@extends('layouts.app')
@section('title', 'Pelanggan')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Pemantauan</p>
            <h1>Pelanggan</h1>
            <p>Pelanggan, undangan, dan transaksi.</p>
        </div>
    </section>
    <section class="panel">
        <div class="table-scroll">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Pelanggan</th>
                        <th>Email</th>
                        <th>Undangan</th>
                        <th>Pembayaran</th>
                        <th>Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customers->firstItem() + $loop->index }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->invitations_count }}</td>
                            <td>{{ $customer->payments_count }}</td>
                            <td>{{ $customer->created_at->format('d M Y') }}</td>
                    </tr>@empty<tr>
                            <td colspan="6">Belum ada pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>{{ $customers->links() }}
    </section>
@endsection
