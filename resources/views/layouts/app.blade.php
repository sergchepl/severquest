<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SeverQuest | Идеальное развлечение для круга друзей</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
    <main id="app">
        @yield('content')
        <notifications position="top center" classes="notification" group="info" />
    </main>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
