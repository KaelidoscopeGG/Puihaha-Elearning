<!doctype html>
<html lang="en" data-theme="dark"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head><body class="login-page">
    <main class="container login-wrap">
        <article class="login-card">
            <p class="eyebrow">PUIHAHA E-LEARNING</p><h1>Create an account</h1>
            <p class="muted">Register to access the administration system.</p>
            <p class="admin-notice">New accounts currently receive administrator access.</p>
            @if ($errors->any())<div class="error-text"><strong>Please check your details:</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <form method="POST" action="{{ route('register.store') }}">
                @csrf
                <label>Name<input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="255" required autofocus></label>
                <label>Email address<input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
                <label>Password<input type="password" name="password" autocomplete="new-password" minlength="8" required><small class="muted">Use at least 8 characters.</small></label>
                <label>Confirm password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="8" required></label>
                <button type="submit">Create account</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </article>
    </main>
</body></html>
