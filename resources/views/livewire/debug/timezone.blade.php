<div wire:poll.1s>
    <x-slot name="header">
        <header>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-gray-950 sm:text-2xl">Debug Time Zone - {{ config('app.name') }}</h1>

                <p class="mt-1 text-sm font-medium">
                    The purpose of this page is to help debut the current issues around time zones and local time. The table below displays an output of the applications current configuration.
                </p>
            </div>
        </header>
    </x-slot>

    <div class="space-y-6">
        <div class="overflow-hidden bg-white shadow sm:rounded-md">
            <div class="p-4 bg-white border-b border-gray-200 sm:px-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Timezone</h3>
            </div>

            <ul role="list" class="divide-y divide-gray-200">
                <li class="px-4 py-4 sm:px-6">
                    <p class="text-sm font-medium text-gray-900">PHP time zone</p>
                    <p class="text-sm text-gray-500 truncate">{{ date_default_timezone_get() }}</p>
                </li>

                <li class="px-4 py-4 sm:px-6">
                    <p class="text-sm font-medium text-gray-900">App time zone</p>
                    <p class="text-sm text-gray-500 truncate">{{ config('app.timezone') }}</p>
                </li>

                <li class="px-4 py-4 sm:px-6">
                    <p class="text-sm font-medium text-gray-900">Settings time zone</p>
                    <p class="text-sm text-gray-500 truncate">{{ $settings['timezone'] }} ({{ \Carbon\Carbon::create($settings['timezone'])->format('P') }})</p>
                </li>
            </ul>
        </div>

        <div class="overflow-hidden bg-white shadow sm:rounded-md">
            <div class="p-4 bg-white border-b border-gray-200 sm:px-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Time</h3>
            </div>

            <ul role="list" class="divide-y divide-gray-200">
                <li class="px-4 py-4 sm:px-6">
                    <p class="text-sm font-medium text-gray-900">UTC time</p>
                    <p class="text-sm text-gray-500 truncate">{{ \Carbon\Carbon::now() }}</p>
                </li>

                <li class="px-4 py-4 sm:px-6">
                    <p class="text-sm font-medium text-gray-900">Local time</p>
                    <p class="text-sm text-gray-500 truncate">{{ \Carbon\Carbon::now()->timezone($settings['timezone'] ?? 'UTC') }}</p>
                </li>
            </ul>
        </div>

        @isset($latest)
            <div class="overflow-hidden bg-white shadow sm:rounded-md">
                <div class="p-4 bg-white border-b border-gray-200 sm:px-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Latest result</h3>
                </div>

                <ul role="list" class="divide-y divide-gray-200">
                    <li class="px-4 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-900">Latest result ran at</p>
                        <p class="text-sm text-gray-500 truncate">{{ $latest->created_at->timezone($settings['db_has_timezone'] ? null : $settings['timezone'] ?? 'UTC')->format('M. jS, Y h:i:s') }}</p>
                    </li>

                    <li class="px-4 py-4 sm:px-6">
                        <p class="text-sm font-medium text-gray-900">Diff for humans</p>
                        <p class="text-sm text-gray-500 truncate">{{ $latest->created_at->diffForHumans() }}</p>
                    </li>
                </ul>
            </div>
        @endisset
    </div>
</div>
