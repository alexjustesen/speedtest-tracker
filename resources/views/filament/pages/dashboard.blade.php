<x-filament-panels::page class="admin-panel-dashboard-page">
    <div class="space-y-6 md:space-y-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
                <x-tabler-chart-bar class="size-5" />
                Platform statistics
            </h2>

            {{-- <x-filament::section class="col-span-full">
                <div class="flex items-center justify-between">
                    <p class="text-sm/6 font-medium text-zinc-500">Quota Usage</p>
                    <a href="#" class="text-sm font-medium text-zinc-600 hover:text-amber-500 underline">Edit</a>
                </div>

                <div class="mt-2">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-body">Bandwidth</span>
                        <span class="text-sm font-medium text-body">450MB of 1 GB</span>
                    </div>

                    <div class="w-full bg-zinc-200 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
            </x-filament::section> --}}

            @filled($this->nextSpeedtest)
                <x-filament::section class="col-span-1" wire:poll.60s>
                    <x-slot name="heading">
                        Next Speedtest in
                    </x-slot>

                    <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100" title="{{ $this->nextSpeedtest->format('F jS, Y g:i A') }}">{{ $this->nextSpeedtest->diffForHumans() }}</p>
                </x-filament::section>
            @else
                <x-filament::section class="col-span-1 bg-zinc-100 shadow-none">
                    <x-slot name="heading">
                        Next Speedtest in
                    </x-slot>

                    <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">No scheduled speedtests</p>
                </x-filament::section>
            @endfilled

            <x-filament::section class="col-span-1">
                <x-slot name="heading">
                    Total tests
                </x-slot>

                <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->resultsStats['total'] }}</p>
            </x-filament::section>

            <x-filament::section class="col-span-1">
                <x-slot name="heading">
                    Total successful tests
                </x-slot>

                <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->resultsStats['completed'] }}</p>
            </x-filament::section>

            <x-filament::section class="col-span-1">
                <x-slot name="heading">
                    Total failed tests
                </x-slot>

                <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->resultsStats['failed'] }}</p>
            </x-filament::section>
        </div>

        @filled($this->latestResult)
            <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
                    <x-tabler-rocket class="size-5" />
                    Latest result
                </h2>

                <x-filament::section class="col-span-1">
                    <x-slot name="heading">
                        Benchmark status
                    </x-slot>

                    <div class="flex items-center gap-x-2">
                        @if($this->latestResult->healthy === true)
                            <div class="flex-none rounded-full bg-emerald-500/20 p-1">
                                <div class="size-2 rounded-full bg-emerald-500"></div>
                            </div>

                            <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Healthy</span>
                        @elseif($this->latestResult->healthy === false)
                            <div class="flex-none rounded-full bg-amber-500/20 p-1">
                                <div class="size-2 rounded-full bg-amber-500"></div>
                            </div>

                            <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Unhealthy</span>
                        @else
                            <div class="flex-none rounded-full bg-zinc-500/20 p-1">
                                <div class="size-2 rounded-full bg-zinc-500"></div>
                            </div>

                            <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Not measured</span>
                        @endif
                    </div>
                </x-filament::section>

                <x-filament::section class="col-span-1">
                    <x-slot name="heading">
                        {{ __('general.download') }}
                    </x-slot>

                    @php
                        $downloadBenchmark = Arr::get($this->latestResult->benchmarks, 'download');
                    @endphp

                    @filled($downloadBenchmark)
                        <x-slot name="afterHeader">
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300 underline decoration-dotted decoration-1 decoration-zinc-500 underline-offset-4" title="Benchmark: {{ true ? 'Passed' : 'Failed' }}">
                                {{ Arr::get($downloadBenchmark, 'value').' '.str(Arr::get($downloadBenchmark, 'unit'))->title() }}
                            </span>
                        </x-slot>
                    @endfilled

                    <p class="flex items-baseline gap-x-2">
                        @php
                            $download = \App\Helpers\Bitrate::formatBits(\App\Helpers\Bitrate::bytesToBits($this->latestResult?->download));

                            $download = explode(' ', $download);
                        @endphp

                        <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $download[0] }}</span>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $download[1].'ps' }}</span>
                    </p>
                </x-filament::section>

                <x-filament::section class="col-span-1">
                    <x-slot name="heading">
                        {{ __('general.upload') }}
                    </x-slot>

                    @php
                        $uploadBenchmark = Arr::get($this->latestResult->benchmarks, 'upload');
                    @endphp

                    @filled($uploadBenchmark)
                        <x-slot name="afterHeader">
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300 underline decoration-dotted decoration-1 decoration-zinc-500 underline-offset-4" title="Benchmark: {{ true ? 'Passed' : 'Failed' }}">
                                {{ Arr::get($uploadBenchmark, 'value').' '.str(Arr::get($uploadBenchmark, 'unit'))->title() }}
                            </span>
                        </x-slot>
                    @endfilled

                    <p class="flex items-baseline gap-x-2">
                        @php
                            $upload = \App\Helpers\Bitrate::formatBits(\App\Helpers\Bitrate::bytesToBits($this->latestResult?->upload));

                            $upload = explode(' ', $upload);
                        @endphp

                        <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $upload[0] }}</span>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $upload[1].'ps' }}</span>
                    </p>
                </x-filament::section>

                <x-filament::section class="col-span-1">
                    <x-slot name="heading">
                        {{ __('general.ping') }}
                    </x-slot>

                    @php
                        $pingBenchmark = Arr::get($this->latestResult->benchmarks, 'ping');
                    @endphp

                    @filled($pingBenchmark)
                        <x-slot name="afterHeader">
                            <span class="text-xs font-medium text-zinc-700 dark:text-zinc-300 underline decoration-dotted decoration-1 decoration-zinc-500 underline-offset-4" title="Benchmark: {{ true ? 'Passed' : 'Failed' }}">
                                {{ Arr::get($pingBenchmark, 'value').' '.str(Arr::get($pingBenchmark, 'unit')) }}
                            </span>
                        </x-slot>
                    @endfilled

                    <p class="flex items-baseline gap-x-2">
                        <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->latestResult?->ping }}</span>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">ms</span>
                    </p>
                </x-filament::section>
            </div>
        @endfilled

        <div class="w-full border-t border-zinc-200 dark:border-zinc-700"></div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-filament::section
                class="col-span-1"
                icon="tabler-book"
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
                        {{ __('general.github_repository') }}
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
