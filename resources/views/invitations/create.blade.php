@extends('layouts.app')
@section('title', 'Buat Undangan')
@section('content')
    <section class="dashboard-intro invitation-editor-intro">
        <div>
            <p class="overline">Undangan Baru</p>
            <h1>Isi cerita pernikahan Anda</h1>
            <p>Ikuti langkah berikut. Data dapat dilengkapi kembali setelah undangan dibuat.</p>
        </div>
        <a class="button secondary" href="{{ route('dashboard') }}">Batal</a>
    </section>
    <form class="invitation-editor-form" method="POST" action="{{ route('invitations.store') }}"
        enctype="multipart/form-data" data-invitation-form novalidate>
        @csrf
        @include('invitations._form', ['invitation' => null])
    </form>
@endsection
