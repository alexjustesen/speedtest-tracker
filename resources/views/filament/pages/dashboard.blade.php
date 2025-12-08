<x-filament-panels::page class="dashboard-page">
    <div class="space-y-6 md:space-y-12">
        <livewire:next-speedtest-banner />

        <livewire:platform-stats />

        <livewire:latest-result-stats />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-filament::section
                class="col-span-1"
                icon="tabler-book"
                icon-size="md"
            >
                <x-slot name="heading">
                    {{ __('general.documentation') }}
                </x-slot>

                <div class="text-sm text-zinc-600 dark:text-zinc-300">
                    <p>Need help getting started or configuring your speedtests?</p>
                </div>

                <div class="mt-5">
                    <x-filament::button
                        href="https://docs.speedtest-tracker.dev?utm_source=app&utm_medium=dashboard&utm_campaign=view_documentation"
                        tag="a"
                        target="_blank"
                    >
                        {{ __('general.view_documentation') }}
                    </x-filament::button>
                </div>
            </x-filament::section>

            <x-filament::section
                class="col-span-1"
                icon="tabler-cash-banknote-heart"
                icon-size="md"
            >
                <x-slot name="heading">
                    {{ __('general.donations') }}
                </x-slot>

                <div class="text-sm text-zinc-600 dark:text-zinc-300">
                    <p>Support the development and maintenance of Speedtest Tracker by making a donation.</p>
                </div>

                <div class="mt-5">
                    <x-filament::button
                        href="https://github.com/sponsors/alexjustesen?utm_source=app&utm_medium=dashboard&utm_campaign=donate"
                        tag="a"
                        target="_blank"
                    >
                        {{ __('general.donate') }}
                    </x-filament::button>
                </div>
            </x-filament::section>

            <x-filament::section
                class="col-span-1"
                icon="tabler-brand-github"
                icon-size="md"
            >
                <x-slot name="heading">
                    {{ __('general.speedtest_tracker') }}
                </x-slot>

                @if (\App\Services\GitHub\Repository::updateAvailable())
                    <x-slot name="afterHeader">
                        <x-filament::badge>
                            {{ __('general.update_available') }}
                        </x-filament::badge>
                    </x-slot>
                @endif

                <ul role="list" class="divide-y divide-zinc-200 space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                    <li class="flex items-center justify-between pb-2">
                        <p class="font-medium">{{ __('general.current_version') }}</p>
                        <p>{{ config('speedtest.build_version') }}</p>
                    </li>

                    <li class="flex items-center justify-between">
                        <p class="font-medium">{{ __('general.latest_version') }}</p>
                        <p>{{ \App\Services\GitHub\Repository::getLatestVersion() }}</p>
                    </li>
                </ul>

                <div class="mt-5">
                    <x-filament::button
                        href="https://github.com/alexjustesen/speedtest-tracker?utm_source=app&utm_medium=dashboard&utm_campaign=github"
                        tag="a"
                        target="_blank"
                    >
                        {{ __('general.github') }} {{ str(__('general.repository'))->lower() }}
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
