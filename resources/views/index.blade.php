<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>

    @include('_partials/errorlist')

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>


    <p>Don't have an account? <a href="{{ route('register') }}">Register here</a>.</p>
</body>
</html>
