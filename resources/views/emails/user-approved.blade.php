<!DOCTYPE html>
<html>

<head>
    <title>User Approved</title>
</head>

<body>
    <header>
        {{-- /LEADS.webp --}}
        <img src="{{ $message->embed(public_path('/LEADS.webp'))}} " alt="">
    </header>
    <h1>Hello, {{ $user->name }}</h1>
    <p>Your account has been approved.</p>
    <a href="{{ route('filament.app.auth.login') }}">
        Click here to login
    </a>
</body>

</html>
