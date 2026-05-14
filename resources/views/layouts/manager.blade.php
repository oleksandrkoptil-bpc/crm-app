<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Manager' }} - CRM</title>
    <link rel="stylesheet" href="{{ asset('css/layouts.css') }}">
    @stack('styles')
</head>
<body>
<div class="page">
    <aside class="sidebar">
        <div class="brand">CRM</div>
        <nav>
            <a class="nav-link {{ request()->routeIs('manager.tickets.*') ? 'active' : '' }}" href="{{ route('manager.tickets.index') }}">
                Tickets
            </a>
            <a class="nav-link" href="{{ url('/api/documentation') }}" target="_blank" rel="noreferrer">
                Swagger
            </a>
        </nav>

        <form class="logout" method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </aside>

    <main class="content">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
