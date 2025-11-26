<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        {{-- Styles --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])

        <script>
            const theme = localStorage.getItem('theme') ?? 'system'

            if (
                theme === 'dark' ||
                (theme === 'system' &&
                    window.matchMedia('(prefers-color-scheme: dark)')
                        .matches)
            ) {
                document.documentElement.classList.add('dark')
            }
        </script>
    </head>
    <body class="antialiased min-h-dvh bg-gray-50 dark:bg-gray-950 text-gray-950 dark:text-white">
        <main class="p-4 sm:p-6 lg:p-8 mx-auto max-w-{{ config('speedtest.content_width') }} space-y-4 sm:space-y-8">
            <header class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ $title ?? 'Dashboard' }}</h1>
                </div>

                <div class="flex gap-3">
                    @if(Route::has('home'))
                        <a href="{{ route('home') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Dashboard V1
                        </a>
                    @endif
                    <a href="{{ url('/admin') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Admin Panel
                    </a>
                </div>
            </header>

            {{ $slot }}
        </main>
    </body>
</html>
