@extends('layouts.app')
@section('title', 'Templates')
@section('content')
<div class="page-head"><div><div class="eyebrow">Administration</div><h1>Templates</h1></div></div>
<div class="grid invitation-list">@foreach($templates as $template)<article class="card invitation-card"><img class="thumb" src="{{ asset($template->thumbnail) }}" alt="Preview {{ $template->name }}" width="640" height="480" decoding="async"><h2>{{ $template->name }}</h2><p class="muted"><code>{{ $template->key }}</code> · {{ $template->invitations_count }} undangan</p><span class="badge {{ $template->is_active ? 'active' : 'inactive' }}">{{ $template->is_active ? 'active' : 'inactive' }}</span></article>@endforeach</div>
@endsection
