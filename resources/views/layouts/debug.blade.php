<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Timezone - {{ config('app.name') }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        {{-- Fonts --}}
        <link href="{{ asset('fonts/inter/inter.css') }}" rel="stylesheet" />

        {{-- Styles --}}
        @filamentStyles
        @vite('resources/css/app.css')
    </head>
    <body class="min-h-screen antialiased bg-gray-50 text-gray-950">
        <main class="max-w-xl p-4 mx-auto space-y-4 sm:p-6 lg:p-8 sm:space-y-8">
            @if (isset($header))
                {{ $header }}
            @endif

            {{ $slot }}
        </main>

        {{-- Scripts --}}
        @filamentScripts
    </body>
</html>
