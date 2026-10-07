@extends('layouts.app')
@section('title', 'Konfirmasi Kehadiran')
@section('content')
    <section class="dashboard-intro">
        <div>
            <p class="overline">Pengelolaan Tamu</p>
            <h1>Konfirmasi Kehadiran</h1>
            <p>Tinjau konfirmasi kehadiran dari seluruh undangan pelanggan.</p>
        </div>
    </section>
    <section class="metric-grid compact-metrics">
        <article class="metric-card"><span class="metric-icon gold"><svg viewBox="0 0 24 24">
                    <path d="M4 5h16v14H4zM4 7l8 6 8-6" />
                </svg></span>
            <div>
                <p>Total Respons</p><strong>{{ number_format($statistics['total']) }}</strong>
            </div>
        </article>
        <article class="metric-card"><span class="metric-icon green"><svg viewBox="0 0 24 24">
                    <path d="m5 12 4 4L19 6" />
                </svg></span>
            <div>
                <p>Hadir</p><strong>{{ number_format($statistics['attending']) }}</strong>
            </div>
        </article>
        <article class="metric-card"><span class="metric-icon blue"><svg viewBox="0 0 24 24">
                    <path d="M12 8v4l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg></span>
            <div>
                <p>Tidak Hadir</p><strong>{{ number_format($statistics['not_attending']) }}</strong>
            </div>
        </article>
        <article class="metric-card"><span class="metric-icon rose"><svg viewBox="0 0 24 24">
                    <path
                        d="M16 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8M17 11h5M19.5 8.5v5" />
                </svg></span>
            <div>
                <p>Perkiraan Tamu</p><strong>{{ number_format($statistics['guests']) }}</strong>
            </div>
        </article>
    </section>
    <section class="panel management-panel">
        <div class="panel-header management-header">
            <div>
                <h2>Semua Respons</h2>
                <p>{{ $rsvps->total() }} respons ditemukan.</p>
            </div>
            <form class="management-filters" method="GET" action="{{ route('admin.rsvps.index') }}"><label><svg
                        viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7" />
                        <path d="m20 20-4-4" />
                    </svg><input type="search" name="search" value="{{ $search }}"
                        placeholder="Cari tamu atau pasangan..."></label><select name="attendance"
                    aria-label="Saring kehadiran">
                    <option value="">Semua kehadiran</option>
                    <option value="attending" @selected($attendance === 'attending')>Hadir</option>
                    <option value="not_attending" @selected($attendance === 'not_attending')>Tidak hadir</option>
                </select><button type="submit">Saring</button>
                @if ($search !== '' || $attendance !== '')
                    <a href="{{ route('admin.rsvps.index') }}">Atur Ulang</a>
                @endif
            </form>
        </div>
        <div class="table-scroll">
            <table class="premium-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tamu</th>
                        <th>Undangan</th>
                        <th>Pelanggan</th>
                        <th>Kehadiran</th>
                        <th>Jumlah</th>
                        <th>Diterima</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rsvps as $rsvp)
                        <tr>
                            <td>{{ $rsvps->firstItem() + $loop->index }}</td>
                            <td>
                                <div class="couple-cell"><span>{{ mb_strtoupper(mb_substr($rsvp->guest_name, 0, 1)) }}</span>
                                    <div><strong>{{ $rsvp->guest_name }}</strong><small>Respons tamu</small></div>
                                </div>
                            </td>
                            <td><strong>{{ $rsvp->invitation->title }}</strong></td>
                            <td>{{ $rsvp->invitation->user->name }}</td>
                            <td><span
                                    class="status-badge {{ $rsvp->attendance === 'attending' ? 'published' : 'inactive' }}"><i></i>{{ $rsvp->attendance === 'attending' ? 'Hadir' : 'Tidak hadir' }}</span>
                            </td>
                            <td>{{ $rsvp->guest_count }}</td>
                            <td>{{ $rsvp->created_at->format('d M Y, H:i') }}</td>
                    </tr>@empty<tr>
                            <td colspan="7">
                                <div class="premium-empty"><span><svg viewBox="0 0 24 24">
                                            <path d="M4 5h16v14H4zM4 7l8 6 8-6" />
                                        </svg></span>
                                    <h3>Tidak ada respons kehadiran</h3>
                                    <p>Respons yang dikirim tamu akan tampil di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
    <div class="pagination">{{ $rsvps->links() }}</div>
@endsection
