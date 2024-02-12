<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body class="min-h-screen antialiased bg-gray-50 dark:bg-gray-950 text-gray-950 dark:text-white">
        <main class="p-4 sm:p-6 lg:p-8 mx-auto max-w-{{ config('speedtest.content_width') }} space-y-4 sm:space-y-8">
            <header class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ $title ?? 'Page Title' }} - {{ config('app.name') }}</h1>
                </div>

                <div class="flex-shrink-0">
                    <x-filament::button
                        href="{{ url('/admin') }}"
                        tag="a"
                    >
                        Admin Panel
                    </x-filament::button>
                </div>
            </header>

            {{ $slot }}
        </main>

        {{-- Scripts --}}
        @filamentScripts
    </body>
</html>
