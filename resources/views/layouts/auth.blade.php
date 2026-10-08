<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="icon" type="image/webp" href="{{ asset('favicon.webp') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-interactions.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-polish.css') }}?v={{ filemtime(public_path('css/app-polish.css')) }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
</head>
<body class="auth-body @yield('auth-page')">
<main class="auth-layout">
    <section class="auth-story" aria-label="Tentang Undangan Digital">
        <img class="auth-story-image" src="{{ asset('images/templates/eternal-ivory/couple-placeholder.svg') }}" alt="" width="960" height="1280" decoding="async">
        <div class="auth-story-overlay"></div>
        <img class="auth-floral auth-floral-top" src="{{ asset('images/templates/eternal-ivory/floral-corner-left.webp') }}" alt="" width="720" height="720" decoding="async">
        <img class="auth-floral auth-floral-bottom" src="{{ asset('images/templates/eternal-ivory/floral-corner-right.webp') }}" alt="" width="720" height="720" decoding="async">
        <div class="auth-story-content">
            <a class="auth-brand" href="{{ route('login') }}" aria-label="Undangan Digital Studio"><img class="brand-wordmark" src="{{ asset('logotext.webp') }}" alt="" width="260" height="115"></a>
            <div class="auth-message"><span class="auth-kicker">Momen indah, dibagikan dengan penuh makna</span><h1>Buat undangan pernikahan elegan dengan mudah.</h1><p>Rancang, sesuaikan, dan bagikan undangan berkesan dari satu ruang kerja yang sederhana.</p><ul><li><svg viewBox="0 0 24 24"><path d="M4 4h16v16H4zM8 8h8M8 12h5M8 16h7"/></svg><span><strong>Templat Premium</strong><small>Desain editorial untuk setiap kisah cinta.</small></span></li><li><svg viewBox="0 0 24 24"><path d="m4 20 4.5-1 10-10-3.5-3.5-10 10zM13.5 6.5 17 10M15 4l2-2 5 5-2 2"/></svg><span><strong>Mudah Disesuaikan</strong><small>Jadikan setiap detail terasa istimewa.</small></span></li><li><svg viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 10.5 6.8-4M8.6 13.5l6.8 4"/></svg><span><strong>Mudah Dibagikan</strong><small>Bagikan undangan melalui satu tautan.</small></span></li></ul></div>
            <p class="auth-quote">“Setiap kisah cinta pantas mendapatkan awal yang indah.”</p>
        </div>
    </section>
    <section class="auth-form-panel">
        <a class="auth-brand mobile-auth-brand" href="{{ route('login') }}"><img class="brand-icon" src="{{ asset('logo.webp') }}" alt="" width="42" height="42"><span><strong>Undangan</strong><small>Digital Studio</small></span></a>
        <div class="auth-form-wrap">
            @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
            @yield('content')
        </div>
        <p class="auth-copyright">© {{ now()->year }} {{ config('app.name') }} · <a href="{{ route('terms') }}">Syarat dan Ketentuan</a> · <a href="{{ route('privacy') }}">Kebijakan Privasi</a></p>
    </section>
</main>
</body>
</html>
