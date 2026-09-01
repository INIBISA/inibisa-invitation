@extends('layouts.app')
@section('title', 'Customers')
@section('content')
    <div class="page-head">
        <div>
            <div class="eyebrow">Administration</div>
            <h1>Customers</h1>
        </div>
    </div>
    <nav class="tabs">
        @foreach (['pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected'] as $key => $label)
            <a class="{{ $status === $key ? 'active' : '' }}"
                href="{{ route('admin.customers.index', ['status' => $key]) }}">{{ $label }}</a>
        @endforeach
    </nav>
    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Undangan</th>
                    <th>Terdaftar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td><strong>{{ $customer->name }}</strong><br><span class="muted">{{ $customer->email }}</span></td>
                        <td><span class="badge {{ $customer->status }}">{{ $customer->status }}</span></td>
                        <td>{{ $customer->invitations_count }}</td>
                        <td>{{ $customer->created_at->format('d M Y') }}</td>
                        <td>
                            <div class="actions">
                                @if ($customer->status !== 'active')
                                    <form method="POST" action="{{ route('admin.customers.update', $customer) }}">@csrf
                                        @method('PATCH')<input type="hidden" name="status" value="active"><button
                                            class="button small" type="submit">Approve</button></form>
                                    @endif @if ($customer->status !== 'rejected')
                                        <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                                            @csrf @method('PATCH')<input type="hidden" name="status"
                                                value="rejected"><button class="button small danger"
                                                type="submit">Reject</button></form>
                                    @endif
                            </div>
                        </td>
                    </tr>
                @empty<tr>
                        <td colspan="5" class="muted">Tidak ada customer pada status ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $customers->links() }}</div>
@endsection
