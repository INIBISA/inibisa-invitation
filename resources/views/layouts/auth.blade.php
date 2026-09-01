<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <main class="auth-wrap">
        <section class="auth-card">
            <div class="eyebrow">Digital Wedding Invitation</div>
            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif@
            @yield('content')
        </section>
    </main>
</body>

</html>
