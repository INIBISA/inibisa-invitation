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
        <div class="auth-story-content">
            <a class="auth-brand" href="{{ route('login') }}" aria-label="Undangan Digital Studio"><img class="brand-wordmark" src="{{ asset('logotext.webp') }}" alt="" width="260" height="115"></a>
            <div class="auth-message"><span class="auth-kicker">Undangan Digital Studio</span><h1>Awal kisah yang indah.</h1></div>
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
