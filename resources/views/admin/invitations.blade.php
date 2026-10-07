@extends('layouts.app')
@section('title', 'Undangan')
@section('content')
    <div class="page-head">
        <div>
            <div class="eyebrow">Administrasi</div>
            <h1>Undangan</h1>
        </div>
    </div>
    <div class="card table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Undangan</th>
                    <th>Pelanggan</th>
                    <th>Templat</th>
                    <th>Status</th>
                    <th>Tautan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invitations as $invitation)
                    <tr>
                        <td>{{ $invitations->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $invitation->title }}</strong><br><span class="muted">{{ $invitation->slug }}</span>
                        </td>
                        <td>{{ $invitation->user->name }}<br><span class="muted">{{ $invitation->user->email }}</span></td>
                        <td>{{ $invitation->template->name }}</td>
                        <td><span class="badge {{ $invitation->status }}">{{ $invitation->statusLabel() }}</span></td>
                        <td>
                            @if ($invitation->status === 'published')
                                <a href="{{ route('public.invitation', $invitation->slug) }}" target="_blank"
                                rel="noopener">Buka</a>@else<span class="muted">Belum publik</span>
                            @endif
                        </td>
                </tr>@empty<tr>
                        <td colspan="6">Belum ada undangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $invitations->links() }}</div>
@endsection
