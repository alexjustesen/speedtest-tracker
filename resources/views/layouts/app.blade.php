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
        @vite('resources/css/dashboard.css')
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

            {{ $slot }}

            @include('layouts.partials.footer')
        </main>

        {{-- Scripts --}}
        @filamentScripts
    </body>
</html>
