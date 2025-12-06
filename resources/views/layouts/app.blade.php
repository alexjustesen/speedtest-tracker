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
            <header class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ $title ?? 'Page Title' }} - {{ config('app.name') }}</h1>
                </div>

                <div class="flex items-center flex-shrink-0 gap-4">
                    <div
                        x-data="{ theme: null }"
                        x-init="
                            theme = localStorage.getItem('theme') || 'system'
                            $watch('theme', () => {
                                localStorage.setItem('theme', theme)
                                const effectiveTheme = theme === 'system'
                                    ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
                                    : theme
                                if (effectiveTheme === 'dark') {
                                    document.documentElement.classList.add('dark')
                                } else {
                                    document.documentElement.classList.remove('dark')
                                }
                                $dispatch('theme-changed', theme)
                            })
                        "
                        class="flex items-center gap-1 p-1 rounded-lg bg-gray-100 dark:bg-gray-800"
                    >
                        <button
                            type="button"
                            x-on:click="theme = 'light'"
                            x-bind:class="{ 'bg-white dark:bg-gray-900 shadow-sm': theme === 'light' }"
                            class="p-2 rounded-md transition-all"
                            aria-label="Light mode"
                        >
                            <x-tabler-sun class="size-4" />
                        </button>

                        <button
                            type="button"
                            x-on:click="theme = 'dark'"
                            x-bind:class="{ 'bg-white dark:bg-gray-900 shadow-sm': theme === 'dark' }"
                            class="p-2 rounded-md transition-all"
                            aria-label="Dark mode"
                        >
                            <x-tabler-moon class="size-4" />
                        </button>

                        <button
                            type="button"
                            x-on:click="theme = 'system'"
                            x-bind:class="{ 'bg-white dark:bg-gray-900 shadow-sm': theme === 'system' }"
                            class="p-2 rounded-md transition-all"
                            aria-label="System theme"
                        >
                            <x-tabler-device-desktop class="size-4" />
                        </button>
                    </div>

                    @auth
                        <x-filament::button
                            href="{{ url('/admin') }}"
                            icon="tabler-layout-dashboard"
                            iconButton="true"
                            tag="a"
                            size="lg"
                        >
                            {{ __('general.admin') }}
                        </x-filament::button>
                    @else
                        <x-filament::button
                            href="{{ url('/login') }}"
                            icon="tabler-login"
                            tag="a"
                            size="lg"
                        >
                            {{ __('auth.sign_in') }}
                        </x-filament::button>
                    @endauth
                </div>
            </header>

            {{ $slot }}
        </main>

        {{-- Scripts --}}
        @filamentScripts
    </body>
</html>
