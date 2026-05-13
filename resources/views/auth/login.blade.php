<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - CRM</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <section class="page-login">
        <form class="card" method="post" action="{{ route('login.store') }}">
            @csrf

            <h1 class="title">CRM Login</h1>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus required>
                @error('email')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <label class="checkbox">
                <input type="checkbox" name="remember" value="1">
                Remember me
            </label>

            <button class="button" type="submit">Login</button>
        </form>
    </section>
</body>

</html>
