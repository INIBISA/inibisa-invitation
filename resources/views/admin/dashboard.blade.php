@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div class="page-head"><div><div class="eyebrow">Administration</div><h1>Dashboard</h1></div></div>
<div class="grid stats">
    <div class="card stat"><span class="muted">Menunggu approval</span><strong>{{ $statistics['pending_customers'] }}</strong><a href="{{ route('admin.customers.index') }}">Periksa customer</a></div>
    <div class="card stat"><span class="muted">Customer aktif</span><strong>{{ $statistics['active_customers'] }}</strong></div>
    <div class="card stat"><span class="muted">Total undangan</span><strong>{{ $statistics['invitations'] }}</strong></div>
    <div class="card stat"><span class="muted">Template</span><strong>{{ $statistics['templates'] }}</strong></div>
</div>
@endsection
