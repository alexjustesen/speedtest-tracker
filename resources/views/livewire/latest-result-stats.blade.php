<div class="latest-result-stats" wire:poll.60s>
    @filled($this->latestResult)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 latest-result-stats">
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
</div>