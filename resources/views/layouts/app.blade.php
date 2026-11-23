<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="site-header">
        <nav class="container">
            <ul><li><a class="brand" href="{{ route('dashboard') }}">Puihaha E-learning</a></li></ul>
            <ul class="nav-links">
                <li><a href="{{ route('members.index') }}">Members</a></li>
                <li><a href="{{ route('categories.index') }}">Categories</a></li>
                <li><a href="{{ route('levels.index') }}">Levels</a></li>
                <li><a href="{{ route('transactions.index') }}">Transactions</a></li>
                <li><a href="{{ route('payments.index') }}">Payments</a></li>
                <li><span class="muted">{{ auth()->user()->name }}</span></li>
                <li><form method="POST" action="{{ route('logout') }}" class="inline-form">@csrf<button class="outline secondary small-button" type="submit">Log out</button></form></li>
            </ul>
        </nav>
    </header>
    <main class="container page-content">
        @if (session('success')) <article class="notice success">{{ session('success') }}</article> @endif
        @if ($errors->any())
            <article class="notice error"><strong>Please check these details:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></article>
        @endif
        @yield('content')
    </main>
    <footer class="container site-footer"><small>Puihaha E-learning · Membership administration</small></footer>
</body>
</html>
