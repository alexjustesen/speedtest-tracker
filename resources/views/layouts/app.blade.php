<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        {{-- Fonts --}}
        <link href="{{ asset('fonts/filament/filament/inter/index.css') }}" rel="stylesheet" />


        {{-- Styles --}}
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        @vite('resources/css/app.css')
        @filamentStyles

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
            @include('layouts.partials.header')

            <div class="flex justify-center">
                <div class="overflow-hidden rounded-lg bg-white dark:bg-zinc-800 shadow-sm">
                    <nav aria-label="Tabs" class="flex space-x-4 px-2 py-1.5">
                        <a href="{{ route('home') }}" @class([
                            'rounded-md px-3 py-2 text-sm font-medium',
                            'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200' => request()->routeIs('home'),
                            'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' => ! request()->routeIs('home'),
                        ])>Dashboard</a>

                        <a href="{{ route('dashboard.results') }}" @class([
                            'rounded-md px-3 py-2 text-sm font-medium',
                            'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-200' => request()->routeIs('dashboard.results'),
                            'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' => ! request()->routeIs('dashboard.results'),
                        ])>Results</a>
                    </nav>
                </div>
            </div>

            {{ $slot }}

            @include('layouts.partials.footer')
        </main>

        {{-- Scripts --}}
        @livewire('notifications')
        @filamentScripts
    </body>
</html>
