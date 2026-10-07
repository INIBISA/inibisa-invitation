@extends('layouts.app')
@section('title', 'Ucapan')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Pesan Tamu</p>
            <h1>Ucapan</h1>
            <p>Baca pesan hangat dari seluruh undangan pernikahan.</p>
        </div>
    </section>
    <section class="panel management-panel">
        <div class="panel-header management-header">
            <div>
                <h2>Semua Ucapan</h2>
                <p>{{ $wishes->total() }} pesan tamu ditemukan.</p>
            </div>
            <form class="management-filters" method="GET" action="{{ route('admin.wishes.index') }}"><label><svg
                        viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m20 20-4-4" />
                    </svg><input type="search" name="search" value="{{ $search }}"
                        placeholder="Cari tamu, pesan, atau pasangan..."></label><input class="input" name="customer"
                    value="{{ request('customer') }}" placeholder="Saring pelanggan"><button class="button secondary"
                    type="submit">Cari</button>
                @if ($search !== '' || request()->filled('customer'))
                    <a href="{{ route('admin.wishes.index') }}">Atur Ulang</a>
                @endif
            </form>
        </div>
        <div class="wish-management-list">
            @forelse($wishes as $wish)
                <article><span class="wish-avatar">{{ mb_strtoupper(mb_substr($wish->guest_name, 0, 1)) }}</span>
                    <div class="wish-content">
                        <div>
                            <strong>{{ $wish->guest_name }}</strong><time>{{ $wish->created_at->format('d M Y, H:i') }}</time>
                        </div>
                        <p>{{ $wish->message }}</p><small>Untuk <b>{{ $wish->invitation->title }}</b> · Pelanggan:
                            {{ $wish->invitation->user->name }}</small>
                    </div>
            </article>@empty<div class="premium-empty"><span><svg viewBox="0 0 24 24">
                            <path
                                d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z" />
                        </svg></span>
                    <h3>Tidak ada ucapan</h3>
                    <p>Pesan yang dikirim tamu akan tampil di sini.</p>
                </div>
            @endforelse
        </div>
    </section>
    <div class="pagination">{{ $wishes->links() }}</div>
@endsection
