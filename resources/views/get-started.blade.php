<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Getting Started - {{ config('app.name') }}</title>

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
    <body class="h-full antialiased bg-gray-50 dark:bg-gray-950 text-gray-950 dark:text-white">
        <main class="flex flex-col justify-center min-h-full p-4 space-y-4 sm:mx-auto sm:p-6 lg:p-8 sm:max-w-3xl sm:space-y-8">
            <header>
                <h1 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">{{ __("Getting Started") }} - {{ config('app.name') }}</h1>
            </header>

            <div>
                <ul role="list" class="divide dark:divide-white/5 divide-white/80">
                    <li class="relative flex items-center py-4 space-x-4">
                        <div class="flex-auto min-w-0">
                            <div class="flex items-center gap-x-3">
                                <div class="flex-none p-1 rounded-full text-amber-400 bg-gray-100/10">
                                    <div class="w-2 h-2 bg-current rounded-full"></div>
                                </div>

                                <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
                                    @auth
                                        <a href="{{ url('/admin') }}" class="flex gap-x-2">
                                            <span class="truncate">Dashboard</span>
                                            <span class="text-amber-400">/</span>
                                            <span class="whitespace-nowrap">Admin Panel</span>
                                            <span class="absolute inset-0"></span>
                                        </a>
                                    @else
                                        <a href="{{ url('/admin/login') }}" class="flex gap-x-2">
                                            <span class="truncate">Sign In</span>
                                            <span class="text-amber-400">/</span>
                                            <span class="whitespace-nowrap">Admin Panel</span>
                                            <span class="absolute inset-0"></span>
                                        </a>
                                    @endauth
                                </h2>
                            </div>

                            <div class="mt-3 flex items-center gap-x-2.5 text-sm leading-5 dark:text-gray-400 text-gray-950">
                                <p class="truncate">
                                    Access the admin panel to run your first speedtest or to setup scheduled tests.
                                </p>
                            </div>
                        </div>

                        <svg class="flex-none w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </li>

                    <li class="relative flex items-center py-4 space-x-4">
                        <div class="flex-auto min-w-0">
                            <div class="flex items-center gap-x-3">
                                <div class="flex-none p-1 rounded-full text-amber-400 bg-gray-100/10">
                                    <div class="w-2 h-2 bg-current rounded-full"></div>
                                </div>

                                <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
                                    <a href="https://docs.speedtest-tracker.dev/" target="_blank" rel="nofollow" class="flex gap-x-2">
                                        <span class="truncate">Getting Started</span>
                                        <span class="text-amber-400">/</span>
                                        <span class="whitespace-nowrap">Docs</span>
                                        <span class="absolute inset-0"></span>
                                    </a>
                                </h2>
                            </div>

                            <div class="mt-3 flex items-center gap-x-2.5 text-sm leading-5 dark:text-gray-400 text-gray-950">
                                <p class="truncate">
                                    Need help getting started with Speedtest Tracker? Check out the docs.
                                </p>
                            </div>
                        </div>

                        <svg class="flex-none w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </li>

                    <li class="relative flex items-center py-4 space-x-4">
                        <div class="flex-auto min-w-0">
                            <div class="flex items-center gap-x-3">
                                <div class="flex-none p-1 rounded-full text-amber-400 bg-gray-100/10">
                                    <div class="w-2 h-2 bg-current rounded-full"></div>
                                </div>

                                <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
                                    <a href="https://github.com/alexjustesen/speedtest-tracker" target="_blank" rel="nofollow" class="flex gap-x-2">
                                        <span class="truncate">GitHub</span>
                                        <span class="text-amber-400">/</span>
                                        <span class="whitespace-nowrap">Repo</span>
                                        <span class="absolute inset-0"></span>
                                    </a>
                                </h2>
                            </div>
                        </div>

                        <svg class="flex-none w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </li>

                    <li class="relative flex items-center py-4 space-x-4">
                        <div class="flex-auto min-w-0">
                            <div class="flex items-center gap-x-3">
                                <div class="flex-none p-1 rounded-full text-amber-400 bg-gray-100/10">
                                    <div class="w-2 h-2 bg-current rounded-full"></div>
                                </div>

                                <h2 class="min-w-0 text-sm font-semibold leading-6 text-white">
                                    <a href="https://github.com/alexjustesen/speedtest-tracker/discussions" target="_blank" rel="nofollow" class="flex gap-x-2">
                                        <span class="truncate">Discussions</span>
                                        <span class="text-amber-400">/</span>
                                        <span class="whitespace-nowrap">Community</span>
                                        <span class="absolute inset-0"></span>
                                    </a>
                                </h2>
                            </div>
                        </div>

                        <svg class="flex-none w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </li>

                    <!-- More items... -->
                  </ul>
            </div>
        </main>

        {{-- Scripts --}}
        @filamentScripts
    </body>
</html>
