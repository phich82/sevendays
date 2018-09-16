<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
</head>
<body>
    <h1 style="text-align: center;">Welcome <strong>{{ $user->name }}</strong> to our website.</h1>
    <p style="text-align: center;">
        <a href="{{ route('login') }}">Login</a>
    </p>
</body>
</html>
