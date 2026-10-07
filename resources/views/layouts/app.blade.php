<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-interactions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-polish.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>

<body class="dashboard-body">
    @php
        $isAdmin = auth()->user()->isAdmin();
        $navigation = $isAdmin
            ? [
                ['route' => 'admin.dashboard', 'label' => 'Dasbor', 'icon' => 'M3 10.5 12 3l9 7.5V21h-6v-7H9v7H3z'],
                [
                    'route' => 'admin.customers.index',
                    'label' => 'Pelanggan',
                    'icon' =>
                        'M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8M17 11h6M20 8v6',
                ],
                ['route' => 'admin.payments.index', 'label' => 'Pembayaran', 'icon' => 'M2 6h20v13H2zM2 10h20M6 15h5'],
                ['route' => 'admin.invitations.index', 'label' => 'Undangan', 'icon' => 'M3 5h18v14H3zM3 7l9 7 9-7'],
                [
                    'route' => 'admin.templates.index',
                    'label' => 'Templat',
                    'icon' => 'M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM13 13h8v8h-8z',
                ],
                [
                    'route' => 'admin.music.index',
                    'label' => 'Musik Pernikahan',
                    'icon' =>
                        'M9 18V5l12-2v13M9 9l12-2M9 18a3 3 0 1 1-3-3c1.7 0 3 1.3 3 3ZM21 16a3 3 0 1 1-3-3c1.7 0 3 1.3 3 3Z',
                ],
                [
                    'route' => 'admin.guests.index',
                    'label' => 'Tamu',
                    'icon' => 'M4 20v-2a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v2M12 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
                ],
                ['route' => 'admin.rsvps.index', 'label' => 'RSVP', 'icon' => 'M4 5h16v15H4zM8 10l2 2 5-5M8 16h8'],
                [
                    'route' => 'admin.wishes.index',
                    'label' => 'Ucapan',
                    'icon' =>
                        'M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z',
                ],
                [
                    'route' => 'admin.settings.edit',
                    'label' => 'Pengaturan',
                    'icon' =>
                        'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM19.4 15l1.2 1.1-2 3.5-1.6-.4a8 8 0 0 1-2 1.1l-.5 1.7h-4l-.5-1.7a8 8 0 0 1-2-1.1l-1.6.4-2-3.5L5.6 15a8 8 0 0 1 0-2l-1.2-1.1 2-3.5L8 8.8a8 8 0 0 1 2-1.1L10.5 6h4l.5 1.7a8 8 0 0 1 2 1.1l1.6-.4 2 3.5-1.2 1.1a8 8 0 0 1 0 2Z',
                ],
            ]
            : [
                ['route' => 'dashboard', 'label' => 'Dasbor', 'icon' => 'M3 10.5 12 3l9 7.5V21h-6v-7H9v7H3z'],
                [
                    'route' => 'templates.index',
                    'label' => 'Pilih Template',
                    'icon' => 'M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM13 13h8v8h-8z',
                ],
                ['route' => 'payments.index', 'label' => 'Pembayaran', 'icon' => 'M2 6h20v13H2zM2 10h20M6 15h5'],
                [
                    'route' => 'customer.guests.index',
                    'label' => 'Tamu Undangan',
                    'icon' => 'M4 20v-2a5 5 0 0 1 5-5h6a5 5 0 0 1 5 5v2M12 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8',
                    'related_routes' => ['invitations.guests.index'],
                ],
                [
                    'route' => 'customer.rsvps.index',
                    'label' => 'RSVP',
                    'icon' => 'M4 5h16v15H4zM8 10l2 2 5-5M8 16h8',
                    'related_routes' => ['invitations.rsvps'],
                ],
                [
                    'route' => 'customer.wishes.index',
                    'label' => 'Ucapan',
                    'icon' => 'M20.8 4.6a5 5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.7-7.5a5.5 5.5 0 0 0 1.1-8.9Z',
                    'related_routes' => ['invitations.wishes'],
                ],
            ];
    @endphp
    <div class="dashboard-shell">
        <div class="sidebar-overlay" data-sidebar-overlay></div>
        <aside class="sidebar" data-sidebar id="app-sidebar">
            <div class="sidebar-brand">
                <a href="{{ $isAdmin ? route('admin.dashboard') : route('dashboard') }}"><span
                        class="brand-mark">U</span><span><strong>Undangan</strong><small>Digital
                            Studio</small></span></a>
                <button class="icon-button sidebar-close" type="button" data-sidebar-close
                    aria-label="Tutup menu">×</button>
            </div>
            <nav class="sidebar-nav" aria-label="Menu utama">
                <p class="sidebar-label">Ruang Kerja</p>
                @foreach ($navigation as $item)
                    @php($isActive = request()->routeIs($item['route'], str_replace('.index', '.*', $item['route']), ...($item['related_routes'] ?? [])))
                    <a href="{{ route($item['route']) }}"
                        class="{{ $isActive ? 'active' : '' }}"
                        @if ($isActive) aria-current="page" @endif>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="{{ $item['icon'] }}" />
                        </svg><span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
            <div class="sidebar-footer">
                <div class="sidebar-person"><span>{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <div>
                        <strong>{{ auth()->user()->name }}</strong><small>{{ $isAdmin ? 'Administrator' : 'Pelanggan' }}</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="button danger sidebar-logout"
                        type="submit"><svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9 4H4v16h5M14 8l4 4-4 4M8 12h10" />
                        </svg>Keluar</button></form>
            </div>
        </aside>
        <div class="dashboard-workspace">
            <header class="dashboard-header">
                <button class="icon-button mobile-menu" type="button" data-sidebar-open aria-controls="app-sidebar"
                    aria-expanded="false" aria-label="Buka menu"><svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg></button>
                <div class="header-title">
                    <small>{{ $isAdmin ? 'RUANG KERJA ADMIN' : 'RUANG KERJA PELANGGAN' }}</small><strong>@yield('title', 'Dasbor')</strong>
                </div>
                <div class="header-actions"><span
                        class="header-avatar">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span><span>{{ auth()->user()->name }}</span>
                </div>
            </header>
            <main class="dashboard-main">
                <div class="content-container">
                    @if (session('success'))
                        <div class="alert success" role="status">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert error" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <dialog class="app-dialog" data-confirm-dialog>
        <form method="dialog">
            <h2 data-confirm-title>Konfirmasi</h2>
            <p data-confirm-message>Yakin ingin melanjutkan?</p>
            <div class="actions"><button class="button secondary" value="cancel">Batal</button><button
                    class="button danger" value="confirm" data-confirm-accept>Ya, lanjutkan</button></div>
        </form>
    </dialog>
</body>

</html>
