<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-interactions.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="dashboard-body">
@php($isAdmin = auth()->user()->isAdmin())
<div class="dashboard-shell">
    <div class="sidebar-overlay" data-sidebar-overlay></div>
    <aside class="sidebar" id="dashboard-sidebar" data-sidebar>
        <div class="sidebar-brand">
            <a href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}" aria-label="{{ config('app.name') }}">
                <span class="brand-mark"><span>U</span></span>
                <span><strong>Undangan</strong><small>Digital Studio</small></span>
            </a>
            <button class="icon-button sidebar-close" type="button" data-sidebar-close aria-label="Tutup menu"><svg viewBox="0 0 24 24"><path d="m6 6 12 12M18 6 6 18"/></svg></button>
        </div>

        <nav class="sidebar-nav" aria-label="Navigasi dashboard">
            <p class="sidebar-label">Workspace</p>
            @if($isAdmin)
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><svg viewBox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg><span>Dashboard</span></a>
                <a class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><svg viewBox="0 0 24 24"><path d="M16 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8M22 20v-2a4 4 0 0 0-3-3.87M16 2.13a4 4 0 0 1 0 7.75"/></svg><span>Customers</span>@if(($statistics['pending_customers'] ?? 0) > 0)<em>{{ $statistics['pending_customers'] }}</em>@endif</a>
                <a class="{{ request()->routeIs('admin.invitations.*') ? 'active' : '' }}" href="{{ route('admin.invitations.index') }}"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 7l8 6 8-6"/></svg><span>Invitations</span></a>
                <a class="{{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" href="{{ route('admin.templates.index') }}"><svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v5h-8zM13 10h8v11h-8zM3 13h8v8H3z"/></svg><span>Templates</span></a>
            @else
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg><span>Dashboard</span></a>
                <a class="{{ request()->routeIs('invitations.edit', 'invitations.preview') ? 'active' : '' }}" href="{{ route('dashboard') }}#recent-invitations"><svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 7l8 6 8-6"/></svg><span>Invitations</span></a>
                <a class="{{ request()->routeIs('invitations.create') ? 'active' : '' }}" href="{{ route('invitations.create') }}"><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg><span>Create Invitation</span></a>
            @endif

            <p class="sidebar-label">Manage</p>
            @if($isAdmin)
                <a class="{{ request()->routeIs('admin.rsvps.*') ? 'active' : '' }}" href="{{ route('admin.rsvps.index') }}"><svg viewBox="0 0 24 24"><path d="M5 3v3M19 3v3M4 8h16M5 5h14a2 2 0 0 1 2 2v13H3V7a2 2 0 0 1 2-2Z"/><path d="m8 14 2 2 5-5"/></svg><span>RSVPs</span></a>
                <a class="{{ request()->routeIs('admin.wishes.*') ? 'active' : '' }}" href="{{ route('admin.wishes.index') }}"><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z"/></svg><span>Wishes</span></a>
                <a class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1 1.55V21h-4v-.08A1.7 1.7 0 0 0 9 19.37a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.63 15a1.7 1.7 0 0 0-1.55-1H3v-4h.08A1.7 1.7 0 0 0 4.63 9a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.63a1.7 1.7 0 0 0 1-1.55V3h4v.08A1.7 1.7 0 0 0 15 4.63a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.37 9a1.7 1.7 0 0 0 1.55 1H21v4h-.08a1.7 1.7 0 0 0-1.52 1Z"/></svg><span>Settings</span></a>
            @else
                <span class="sidebar-link disabled" title="Pilih undangan untuk melihat RSVP"><svg viewBox="0 0 24 24"><path d="M5 3v3M19 3v3M4 8h16M5 5h14a2 2 0 0 1 2 2v13H3V7a2 2 0 0 1 2-2Z"/></svg><span>RSVPs</span></span>
                <span class="sidebar-link disabled" title="Pilih undangan untuk melihat wishes"><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z"/></svg><span>Wishes</span></span>
                <span class="sidebar-link disabled"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1 1.55V21h-4v-.08A1.7 1.7 0 0 0 9 19.37a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.63 15a1.7 1.7 0 0 0-1.55-1H3v-4h.08A1.7 1.7 0 0 0 4.63 9a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 9 4.63a1.7 1.7 0 0 0 1-1.55V3h4v.08A1.7 1.7 0 0 0 15 4.63a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.37 9a1.7 1.7 0 0 0 1.55 1H21v4h-.08a1.7 1.7 0 0 0-1.52 1Z"/></svg><span>Settings</span></span>
            @endif
        </nav>

        <div class="sidebar-support"><span class="support-icon"><svg viewBox="0 0 24 24"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20ZM9.1 9a3 3 0 1 1 5.8 1c-.75 1-1.9 1.35-2.4 2.5M12 17h.01"/></svg></span><div><strong>Need help?</strong><small>Contact your administrator</small></div></div>
    </aside>

    <div class="dashboard-workspace">
        <header class="dashboard-header">
            <button class="icon-button mobile-menu" type="button" data-sidebar-open aria-controls="dashboard-sidebar" aria-expanded="false" aria-label="Buka menu"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            <label class="dashboard-search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input type="search" placeholder="Search invitations, customers..." data-dashboard-search><kbd>⌘ K</kbd></label>
            <div class="header-actions">
                <button class="icon-button notification-button" type="button" aria-label="Notifications"><svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg><span></span></button>
                <div class="profile-menu">
                    <button class="profile-trigger" type="button" data-profile-trigger aria-expanded="false"><span class="avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ $isAdmin ? 'Administrator' : 'Customer' }}</small></span><svg class="chevron" viewBox="0 0 24 24"><path d="m8 10 4 4 4-4"/></svg></button>
                    <div class="profile-dropdown" data-profile-dropdown>
                        <div><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><svg viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3M15 3h5a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-5"/></svg>Sign out</button></form>
                    </div>
                </div>
            </div>
        </header>

        <main class="dashboard-main"><div class="content-container">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="alert error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div></main>
    </div>
</div>
</body>
</html>
