<x-filament-panels::page>
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
                <x-filament::section class="col-span-2" wire:poll>
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="flex items-center col-span-full py-3 md:py-6">
                    <div aria-hidden="true" class="w-full border-t border-gray-300"></div>
                    <div class="relative flex justify-center">
                        <span class="bg-gray-50 px-2 text-sm text-gray-500">Latest</span>
                    </div>
                    <div aria-hidden="true" class="w-full border-t border-gray-300"></div>
                </div>

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
    </div>
</x-filament-panels::page>
