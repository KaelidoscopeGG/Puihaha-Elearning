<!doctype html>
<html lang="en" data-theme="dark"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head><body class="login-page">
    <main class="container login-wrap">
        <article class="login-card">
            <p class="eyebrow">MEMBERSHIP ADMINISTRATION</p><h1>Welcome back</h1><p class="muted login-description">Sign in to manage memberships.</p>
            @if ($errors->any())<p class="error-text">{{ $errors->first() }}</p>@endif
            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus></label>
                <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
                <label class="check-row"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
                <button type="submit">Sign in</button>
            </form>
            <p class="auth-switch">Need an account? <a href="{{ route('register') }}">Register here</a></p>
        </article>
    </main>
</body></html>
