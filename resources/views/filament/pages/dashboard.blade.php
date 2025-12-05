<x-filament-panels::page class="admin-panel-dashboard-page">
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-8 gap-6">
            {{-- <x-filament::section class="col-span-full">
                <div class="flex items-center justify-between">
                    <p class="text-sm/6 font-medium text-gray-500">Quota Usage</p>
                    <a href="#" class="text-sm font-medium text-gray-600 hover:text-amber-500 underline">Edit</a>
                </div>

                <div class="mt-2">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-body">Bandwidth</span>
                        <span class="text-sm font-medium text-body">450MB of 1 GB</span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: 45%"></div>
                    </div>
                </div>
            </x-filament::section> --}}

            @filled($this->nextSpeedtest)
                <x-filament::section class="col-span-2" wire:poll.60s>
                    <p class="text-sm/6 font-medium text-gray-500">Next speedtest in</p>

                    <p class="mt-2 flex items-baseline gap-x-2">
                        <span class="text-xl font-semibold tracking-tight text-gray-900" title="{{ $this->nextSpeedtest->format('F jS, Y g:i A') }}">{{ $this->nextSpeedtest->diffForHumans() }}</span>
                    </p>
                </x-filament::section>
            @else
                <x-filament::section class="col-span-2 bg-gray-100 shadow-none">
                    <p class="text-sm/6 font-medium text-gray-500">Next speedtest in</p>

                    <p class="mt-2 flex items-baseline gap-x-2">
                        <span class="text-xl font-semibold tracking-tight text-gray-900">No scheduled speedtests</span>
                    </p>
                </x-filament::section>
            @endfilled

            <x-filament::section class="col-span-2">
                <p class="text-sm/6 font-medium text-gray-500">Total tests</p>

                <p class="mt-2 flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-gray-900">{{ $this->resultsStats['total'] }}</span>
                </p>
            </x-filament::section>

            <x-filament::section class="col-span-2">
                <p class="text-sm/6 font-medium text-gray-500">Total successful tests</p>

                <p class="mt-2 flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-gray-900">{{ $this->resultsStats['completed'] }}</span>
                </p>
            </x-filament::section>

            <x-filament::section class="col-span-2">
                <p class="text-sm/6 font-medium text-gray-500">Total failed tests</p>

                <p class="mt-2 flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-gray-900">{{ $this->resultsStats['failed'] }}</span>
                </p>
            </x-filament::section>
        </div>

        @filled($this->latestResult)
            <div class="w-full border-t border-gray-200"></div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <h3 class="text-base font-semibold text-gray-900 col-span-full">Latest result</h3>

                <x-filament::section class="col-span-1">
                    <p class="text-sm/6 font-medium text-gray-500">Benchmarks</p>

                    <div class="mt-2 flex items-center gap-x-1.5">
                        @if($this->latestResult->healthy === true)
                            <div class="flex-none rounded-full bg-emerald-500/20 p-1">
                                <div class="size-1.5 rounded-full bg-emerald-500"></div>
                            </div>

                            <span class="text-xl font-semibold tracking-tight text-gray-900">Healthy</span>
                        @elseif($this->latestResult->healthy === false)
                            <div class="flex-none rounded-full bg-amber-500/20 p-1">
                                <div class="size-1.5 rounded-full bg-amber-500"></div>
                            </div>

                            <span class="text-xl font-semibold tracking-tight text-gray-900">Unhealthy</span>
                        @else
                            <div class="flex-none rounded-full bg-gray-500/20 p-1">
                                <div class="size-1.5 rounded-full bg-gray-500"></div>
                            </div>

                            <span class="text-xl font-semibold tracking-tight text-gray-900">Not measured</span>
                        @endif
                    </div>
                </x-filament::section>

                <x-filament::section class="col-span-1">
                    <div class="flex items-baseline justify-between">
                        @php
                            $downloadBenchmark = Arr::get($this->latestResult->benchmarks, 'download');
                        @endphp

                        <h4 class="text-sm/6 font-medium text-gray-500">Download</h4>

                        @filled($downloadBenchmark)
                            <span class="text-xs font-medium text-gray-700 underline decoration-dotted decoration-1 decoration-gray-500 underline-offset-4" title="Benchmark: {{ true ? 'Passed' : 'Failed' }}">
                                {{ Arr::get($downloadBenchmark, 'value').' '.str(Arr::get($downloadBenchmark, 'unit'))->title() }}
                            </span>
                        @endfilled
                    </div>

                    <p class="mt-2 flex items-baseline gap-x-2">
                        @php
                            $download = \App\Helpers\Bitrate::formatBits(\App\Helpers\Bitrate::bytesToBits($this->latestResult?->download));

                            $download = explode(' ', $download);
                        @endphp

                        <span class="text-xl font-semibold tracking-tight text-gray-900">{{ $download[0] }}</span>
                        <span class="text-sm text-gray-500">{{ $download[1].'ps' }}</span>
                    </p>
                </x-filament::section>

                <x-filament::section class="col-span-1">
                    <div class="flex items-baseline justify-between">
                        @php
                            $uploadBenchmark = Arr::get($this->latestResult->benchmarks, 'upload');
                        @endphp

                        <h4 class="text-sm/6 font-medium text-gray-500">Upload</h4>

                        @filled($uploadBenchmark)
                            <span class="text-xs font-medium text-gray-700 underline decoration-dotted decoration-1 decoration-gray-500 underline-offset-4" title="Benchmark: {{ true ? 'Passed' : 'Failed' }}">
                                {{ Arr::get($uploadBenchmark, 'value').' '.str(Arr::get($uploadBenchmark, 'unit'))->title() }}
                            </span>
                        @endfilled
                    </div>

                    <p class="mt-2 flex items-baseline gap-x-2">
                        @php
                            $upload = \App\Helpers\Bitrate::formatBits(\App\Helpers\Bitrate::bytesToBits($this->latestResult?->upload));

                            $upload = explode(' ', $upload);
                        @endphp

                        <span class="text-xl font-semibold tracking-tight text-gray-900">{{ $upload[0] }}</span>
                        <span class="text-sm text-gray-500">{{ $upload[1].'ps' }}</span>
                    </p>
                </x-filament::section>

                <x-filament::section class="col-span-1">
                    <div class="flex items-baseline justify-between">
                        @php
                            $pingBenchmark = Arr::get($this->latestResult->benchmarks, 'ping');
                        @endphp

                        <h4 class="text-sm/6 font-medium text-gray-500">Ping</h4>

                        @filled($pingBenchmark)
                            <span class="text-xs font-medium text-gray-700 underline decoration-dotted decoration-1 decoration-gray-500 underline-offset-4" title="Benchmark: {{ true ? 'Passed' : 'Failed' }}">
                                {{ Arr::get($pingBenchmark, 'value').str(Arr::get($pingBenchmark, 'unit')) }}
                            </span>
                        @endfilled
                    </div>

                    <p class="mt-2 flex items-baseline gap-x-2">
                        <span class="text-xl font-semibold tracking-tight text-gray-900">{{ $this->latestResult?->ping }}</span>
                        <span class="text-sm text-gray-500">ms</span>
                    </p>
                </x-filament::section>
            </div>
        @endfilled

        <div class="w-full border-t border-gray-200"></div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-filament::section class="col-span-1" icon="lucide-book-open-text">
                <x-slot name="heading">
                    {{ __('general.documentation') }}
                </x-slot>

                <div class="text-sm text-gray-600 dark:text-gray-300">
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

            <x-filament::section class="col-span-1 h-full" icon="lucide-hand-coins">
                <x-slot name="heading">
                    {{ __('general.donations') }}
                </x-slot>

                <div class="text-sm text-gray-600 dark:text-gray-300">
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
                icon="lucide-rabbit"
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

                <ul role="list" class="divide-y divide-gray-200 space-y-2 text-sm text-gray-600 dark:text-gray-300">
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
