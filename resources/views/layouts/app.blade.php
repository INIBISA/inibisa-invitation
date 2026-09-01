<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body>
<header class="topbar">
    <div class="shell topbar-inner">
        <a class="brand" href="{{ auth()->user()?->isAdmin() ? route('admin.dashboard') : route('dashboard') }}">UNDANGAN DIGITAL</a>
        <nav class="nav" aria-label="Navigasi utama">
            @if(auth()->user()?->isAdmin())
                <a href="{{ route('admin.dashboard') }}">Dashboard</a><a href="{{ route('admin.customers.index') }}">Customers</a><a href="{{ route('admin.invitations.index') }}">Invitations</a><a href="{{ route('admin.templates.index') }}">Templates</a>
            @else
                <a href="{{ route('dashboard') }}">Undangan Saya</a><a href="{{ route('invitations.create') }}">Buat Undangan</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="link-button" type="submit">Keluar</button></form>
        </nav>
    </div>
</header>
<main><div class="shell">
    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</div></main>
</body>
</html>
