@extends('layouts.auth')
@section('title', 'Status Akun')
@section('content')<h1>Akses belum aktif</h1><p class="muted">Akun Anda belum disetujui atau sudah dinonaktifkan. Anda belum dapat mengakses dashboard atau mengelola undangan. Silakan hubungi admin untuk menanyakan status akun.</p><form method="POST" action="{{ route('logout') }}">@csrf<button class="button" type="submit">Keluar dari akun</button></form>@endsection
