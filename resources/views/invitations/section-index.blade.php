@extends('layouts.app')
@section('title', $title)
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Kelola Undangan</p>
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
    </section>
    <section class="panel">
        <div class="panel-header">
            <div>
                <h2>Pilih Undangan</h2>
                <p>Pilih undangan yang ingin dikelola.</p>
            </div>
        </div>
        @if ($invitations->total() === 0)
            <div class="premium-empty dashboard-empty">
                <span class="empty-symbol">✦</span>
                <h3>Belum punya undangan</h3>
                <p>Buat undangan terlebih dahulu untuk mengelola {{ strtolower($title) }}.</p>
                <a class="button gold" href="{{ route('templates.index') }}">Pilih Templat</a>
            </div>
        @else
            <div class="table-scroll">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th>Undangan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invitations as $invitation)
                            <tr>
                                <td><strong>{{ $invitation->title }}</strong><br><span class="muted">{{ $invitation->slug }}</span></td>
                                <td><span class="status-badge {{ $invitation->status }}"><i></i>{{ $invitation->statusLabel() }}</span></td>
                                <td><a class="button secondary small" href="{{ route($invitationRoute, $invitation) }}">Lihat {{ $title }}</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $invitations->links() }}</div>
        @endif
    </section>
@endsection
