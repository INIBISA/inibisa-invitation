@extends('layouts.auth')
@section('title', 'Status Akun')
@section('content')<h1>Akses belum aktif</h1><p class="muted">Akun Anda belum disetujui atau sudah dinonaktifkan. Silakan hubungi admin.</p><form method="POST" action="{{ route('logout') }}">@csrf<button class="button" type="submit">Kembali ke login</button></form>@endsection
