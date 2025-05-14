<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-dvh">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title }} - {{ config('app.name') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        {{-- Fonts --}}
        <link href="{{ asset('fonts/inter/inter.css') }}" rel="stylesheet" />

        {{-- Styles --}}
        @filamentStyles
        @vite('resources/css/app.css')

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
        {{ $slot }}

        {{-- Scripts --}}
        @filamentScripts
    </body>
</html>
