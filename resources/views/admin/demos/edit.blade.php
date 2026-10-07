@extends('layouts.app')
@section('title', 'Edit Demo ' . $demo->title)
@section('content')
    <section class="dashboard-intro invitation-editor-intro">
        <div>
            <p class="overline">{{ $demo->template->name }}</p>
            <h1>Ubah Demo Templat</h1>
            <p>Atur seluruh konten, media, musik, dan contoh ucapan yang dilihat calon pelanggan.</p>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('admin.templates.index') }}">Kembali</a>
            <a class="button gold" href="{{ route('templates.show', $demo->template) }}" target="_blank" rel="noopener">Pratinjau Demo</a>
        </div>
    </section>

    <div class="demo-editor-notice">
        <span>Demo Publik</span>
        <div><strong>{{ $demo->title }}</strong><small>{{ route('templates.show', $demo->template) }}</small></div>
        <p>Perubahan langsung terlihat pada halaman demo setelah disimpan.</p>
    </div>

    <form class="invitation-editor-form" method="POST" action="{{ route('admin.demos.update', $demo) }}"
        enctype="multipart/form-data" data-invitation-form novalidate>
        @csrf
        @method('PUT')
        @include('invitations._form', [
            'invitation' => $demo,
            'templates' => collect([$demo->template]),
            'musicChoices' => collect(),
            'isDemoEditor' => true,
        ])
    </form>
@endsection
