<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name', 'FleetOps') }} — @yield('title', 'Welcome')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet" />

    <!-- Styles via Vite -->
    @vite(['resources/css/app.css'])
</head>
<body>

    @yield('content')

    <!-- Scripts via Vite -->
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>
</html>
