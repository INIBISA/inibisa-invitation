@extends('layouts.app')
@section('title', 'Templates')
@section('content')
<div class="page-head"><div><div class="eyebrow">Administration</div><h1>Templates</h1></div></div>
<div class="grid invitation-list">
@foreach($templates as $template)
@php($demo = ($demos ?? collect())->get($template->id))
<article class="card invitation-card">
<img class="thumb" src="{{ asset($template->thumbnail) }}" alt="Preview {{ $template->name }}" width="640" height="480" decoding="async">
<h2>{{ $template->name }}</h2>
<p class="muted"><code>{{ $template->key }}</code> · {{ $template->invitations_count }} undangan</p>
<span class="badge {{ $template->is_active ? 'active' : 'inactive' }}">{{ $template->is_active ? 'active' : 'inactive' }}</span>
<div class="actions" style="margin-top:12px">
@if($demo && $template->is_active)
<a class="button small gold" href="{{ route('public.invitation', $demo->slug) }}" target="_blank" rel="noopener">Lihat Demo</a>
<a class="button small secondary" href="{{ route('admin.demos.edit', $demo->id) }}">Edit Demo</a>
@elseif($demo && ! $template->is_active)
<a class="button small secondary" href="{{ route('admin.demos.edit', $demo->id) }}">Edit Demo</a>
<span class="muted">Aktifkan template untuk demo publik</span>
@else
<span class="muted">Belum ada demo</span>
@endif
</div>
</article>
@endforeach
</div>
@endsection
