<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TruckDispatch')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body>

<div class="app-shell">

    @php
        $unassignedCount     = \App\Models\Order::where('status','confirmed')->count();
        $unreadNotifCount    = auth()->user()->unreadNotifications()->count();
        $recentNotifications = auth()->user()->notifications()->latest()->limit(6)->get();
    @endphp

    @include('layouts.partials.sidebar')

    <div class="app-main">
        @include('layouts.partials.topbar')
        <main class="app-content">
            @yield('content')
        </main>
    </div>

</div>

@livewireScripts
@stack('scripts')
</body>
</html>
