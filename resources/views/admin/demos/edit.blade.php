@extends('layouts.app')
@section('title', 'Edit Demo '.$invitation->title)
@section('content')
<div class="page-head"><div><div class="eyebrow">Edit Demo</div><h1>{{ $invitation->title }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('admin.templates.index') }}">Kembali</a><a class="button gold" href="{{ route('public.invitation', $invitation->slug) }}" target="_blank" rel="noopener">Lihat Demo</a></div></div>
@if($invitation->media->isNotEmpty())
<section class="card form-section"><h2>Media saat ini</h2><div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(120px,1fr))">
@foreach($invitation->media as $medium)
@if($medium->collection === 'music')
<div><span class="muted">Musik:</span><br><code>{{ basename($medium->file_path) }}</code></div>
@else
<div><img src="{{ asset('storage/'.$medium->file_path) }}" alt="{{ $medium->collection }}" style="width:100%;height:90px;object-fit:cover;border-radius:8px" loading="lazy"><br><span class="muted">{{ $medium->collection }}</span></div>
@endif
@endforeach
</div><p class="muted">Upload file baru pada form di bawah untuk mengganti media per koleksi.</p></section>
@endif
<form method="POST" action="{{ route('admin.demos.update', $invitation) }}" enctype="multipart/form-data">@csrf @method('PUT') @include('invitations._form', ['backUrl' => route('admin.templates.index')])</form>
@endsection
