<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($pageTitle) ? $pageTitle . ' · ' . config('app.name', 'Osaka Sticker Tracker') : config('app.name', 'Osaka Sticker Tracker') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Track and manage sticker placements across the city.' }}">
        <meta name="theme-color" content="#C41E3A">

        {{-- Open Graph --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name', 'Osaka Sticker Tracker') }}">
        <meta property="og:title" content="{{ $metaTitle ?? config('app.name', 'Osaka Sticker Tracker') }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Track and manage sticker placements across the city.' }}">
        <meta property="og:image" content="{{ $metaImage ?? asset('images/osaka.png') }}">
        <meta property="og:url" content="{{ url()->current() }}">

        {{-- Twitter / Discord --}}
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="{{ $metaTitle ?? config('app.name', 'Osaka Sticker Tracker') }}">
        <meta name="twitter:description" content="{{ $metaDescription ?? 'Track and manage sticker placements across the city.' }}">
        <meta name="twitter:image" content="{{ $metaImage ?? asset('images/osaka.png') }}">

        @stack('meta')

        <link rel="icon" type="image/png" href="{{ asset('images/osaka.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-osaka-cream flex flex-col">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white border-b border-gray-100">
                    <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="flex-1">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>

            <!-- Footer -->
            <footer class="bg-osaka-charcoal text-osaka-cream/60 mt-auto">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/osaka.png') }}" alt="Osaka" class="h-6 w-6 opacity-70">
                            <span class="text-sm font-medium">Osaka Sticker Tracker</span>
                        </div>
                        <div class="text-xs">
                            Tracking stickers across the city &bull; {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
