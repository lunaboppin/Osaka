<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Osaka Sticker Tracker') }}</title>
        <meta name="description" content="Track and manage sticker placements across the city.">
        <meta name="theme-color" content="#C41E3A">

        {{-- Open Graph --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'Osaka Sticker Tracker') }}">
        <meta property="og:title" content="{{ config('app.name', 'Osaka Sticker Tracker') }}">
        <meta property="og:description" content="Track and manage sticker placements across the city.">
        <meta property="og:image" content="{{ asset('images/osaka.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">

        {{-- Twitter / Discord --}}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ config('app.name', 'Osaka Sticker Tracker') }}">
        <meta name="twitter:description" content="Track and manage sticker placements across the city.">
        <meta name="twitter:image" content="{{ asset('images/osaka.png') }}">

        <link rel="icon" type="image/png" href="{{ asset('images/osaka.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
