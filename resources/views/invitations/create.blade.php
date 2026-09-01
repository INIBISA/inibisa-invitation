@extends('layouts.app')
@section('title', 'Buat Undangan')
@section('content')
<div class="page-head"><div><div class="eyebrow">Undangan Baru</div><h1>Pilih dan isi cerita</h1></div><a class="button secondary" href="{{ route('dashboard') }}">Batal</a></div>
<form method="POST" action="{{ route('invitations.store') }}" enctype="multipart/form-data">@csrf @include('invitations._form', ['invitation' => null])</form>
@endsection
