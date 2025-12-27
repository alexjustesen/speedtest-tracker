<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }} - {{ config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <script>
            const theme = localStorage.getItem('theme') ?? 'system'
            const effectiveTheme = theme === 'system'
                ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                : theme

            if (effectiveTheme === 'dark') {
                document.documentElement.classList.add('dark')
            }
        </script>
    </head>
    <body class="min-h-screen bg-white dark:bg-neutral-950 antialiased">
        @include('partials.header')

        <flux:main container>
            {{ $slot }}
        </flux:main>

        @fluxScripts
        @livewireScriptConfig
    </body>
</html>
