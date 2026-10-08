@extends('layouts.app')
@section('title', 'Ubah ' . $invitation->title)
@section('content')
    <section class="dashboard-intro invitation-editor-intro">
        <div>
            <p class="overline">Ubah Undangan</p>
            <h1>{{ $invitation->title }}</h1>
            <p>Perbarui informasi, media, dan pengaturan undangan Anda.</p>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('invitations.preview', $invitation) }}" target="_blank"
                rel="noopener">Pratinjau</a>
            @if ($invitation->status === 'published')
                <a class="button gold" href="{{ route('public.invitation', $invitation->slug) }}" target="_blank"
                    rel="noopener">Buka Tautan</a>
            @endif
        </div>
    </section>

    <section class="panel invitation-publication">
        <div>
            <p class="overline">Publikasi</p>
            <h2>Bagikan undangan</h2>
            <p>Status saat ini: <span class="status-badge {{ $invitation->status }}"><i></i>{{ $invitation->statusLabel() }}</span></p>
            <small>Pastikan nama mempelai, tanggal pernikahan, dan minimal satu acara sudah lengkap sebelum publikasi.</small>
        </div>
        <div class="actions">
            @if ($invitation->status === 'published')
                <form method="POST" action="{{ route('invitations.publication.destroy', $invitation) }}"
                    data-confirm="Nonaktifkan link undangan ini?">@csrf @method('DELETE')<button class="button secondary"
                        type="submit">Nonaktifkan</button></form>
                <button class="button secondary" type="button"
                    data-copy-link="{{ route('public.invitation', $invitation->slug) }}">Salin Tautan</button>
            @else
                <form method="POST" action="{{ route('invitations.publication.store', $invitation) }}"
                    data-confirm="Publikasikan undangan ini sekarang?">@csrf<button class="button gold"
                        type="submit">Publikasikan</button></form>
            @endif
            <form method="POST" action="{{ route('invitations.destroy', $invitation) }}"
                data-confirm="Hapus undangan dan semua medianya?">@csrf @method('DELETE')<button class="button danger"
                    type="submit">Hapus Undangan</button></form>
        </div>
    </section>

    <form class="invitation-editor-form" method="POST" action="{{ route('invitations.update', $invitation) }}"
        enctype="multipart/form-data" data-invitation-form data-step-update-url="{{ route('invitations.steps.update', ['invitation' => $invitation, 'step' => '__STEP__']) }}" novalidate>
        @csrf
        @method('PUT')
        @include('invitations._form')
    </form>
@endsection
