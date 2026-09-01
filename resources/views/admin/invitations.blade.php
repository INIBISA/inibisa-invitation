@extends('layouts.app')
@section('title', 'Invitations')
@section('content')
<div class="page-head"><div><div class="eyebrow">Administration</div><h1>Invitations</h1></div></div>
<div class="card table-wrap"><table class="table"><thead><tr><th>Undangan</th><th>Customer</th><th>Template</th><th>Status</th><th>Link</th></tr></thead><tbody>@forelse($invitations as $invitation)<tr><td><strong>{{ $invitation->title }}</strong><br><span class="muted">{{ $invitation->slug }}</span></td><td>{{ $invitation->user->name }}<br><span class="muted">{{ $invitation->user->email }}</span></td><td>{{ $invitation->template->name }}</td><td><span class="badge {{ $invitation->status }}">{{ $invitation->status }}</span></td><td>@if($invitation->status === 'published')<a href="{{ route('public.invitation', $invitation->slug) }}" target="_blank" rel="noopener">Buka</a>@else<span class="muted">Belum publik</span>@endif</td></tr>@empty<tr><td colspan="5">Belum ada undangan.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $invitations->links() }}</div>
@endsection
