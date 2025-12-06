<div wire:poll.60s>
    @filled($this->latestResult)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 latest-result-stats">
            <div class="col-span-full">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            <x-tabler-rocket class="size-5" />
                            Latest result
                        </h2>

                        <p class="mt-1 text-sm font-medium text-zinc-600 dark:text-zinc-400">{{ $this->latestResult->created_at->format(config('app.datetime_format')) }}</p>
                    </div>

                    @auth
                        <x-filament::button
                            href="{{ url('admin/results') }}"
                            tag="a"
                            size="sm"
                        >
                            {{ __('general.view') }}
                        </x-filament::button>
                    @endauth
                </div>
            </div>

            <x-filament::section class="col-span-1" icon="tabler-download" icon-size="md">
                <x-slot name="heading">
                    {{ __('general.download') }}
                </x-slot>

                @php
                    $downloadBenchmark = Arr::get($this->latestResult->benchmarks, 'download');
                    $downloadBenchmarkPassed = Arr::get($downloadBenchmark, 'passed', false);
                @endphp

                @filled($downloadBenchmark)
                    <x-slot name="afterHeader">
                        <span @class([
                            'inline-flex items-center gap-x-1 text-xs font-medium underline decoration-dotted decoration-1 decoration-zinc-500 underline-offset-4',
                            'text-green-500 dark:text-green-400' => $downloadBenchmarkPassed,
                            'text-amber-500 dark:text-amber-400' => ! $downloadBenchmarkPassed,
                        ]) title="Benchmark {{ $downloadBenchmarkPassed ? 'passed' : 'failed' }}">
                            @if ($downloadBenchmarkPassed)
                                <x-tabler-circle-check class="size-4" />
                            @else
                                <x-tabler-alert-circle class="size-4" />
                            @endif
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

            <x-filament::section class="col-span-1" icon="tabler-upload" icon-size="md">
                <x-slot name="heading">
                    {{ __('general.upload') }}
                </x-slot>

                @php
                    $uploadBenchmark = Arr::get($this->latestResult->benchmarks, 'upload');
                    $uploadBenchmarkPassed = Arr::get($uploadBenchmark, 'passed', false);
                @endphp

                @filled($uploadBenchmark)
                    <x-slot name="afterHeader">
                        <span @class([
                            'inline-flex items-center gap-x-1 text-xs font-medium underline decoration-dotted decoration-1 decoration-zinc-500 underline-offset-4',
                            'text-green-500 dark:text-green-400' => $uploadBenchmarkPassed,
                            'text-amber-500 dark:text-amber-400' => ! $uploadBenchmarkPassed,
                        ]) title="Benchmark {{ $uploadBenchmarkPassed ? 'passed' : 'failed' }}">
                            @if ($uploadBenchmarkPassed)
                                <x-tabler-circle-check class="size-4" />
                            @else
                                <x-tabler-alert-triangle class="size-4" />
                            @endif
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

            <x-filament::section class="col-span-1" icon="tabler-clock-bolt" icon-size="sm">
                <x-slot name="heading">
                    {{ __('general.ping') }}
                </x-slot>

                @php
                    $pingBenchmark = Arr::get($this->latestResult->benchmarks, 'ping');
                    $pingBenchmarkPassed = Arr::get($pingBenchmark, 'passed', false);
                @endphp

                @filled($pingBenchmark)
                    <x-slot name="afterHeader">
                        <span @class([
                            'inline-flex items-center gap-x-1 text-xs font-medium underline decoration-dotted decoration-1 decoration-zinc-500 underline-offset-4',
                            'text-green-500 dark:text-green-400' => $pingBenchmarkPassed,
                            'text-amber-500 dark:text-amber-400' => ! $pingBenchmarkPassed,
                        ]) title="Benchmark {{ $pingBenchmarkPassed ? 'passed' : 'failed' }}">
                            @if ($pingBenchmarkPassed)
                                <x-tabler-circle-check class="size-4" />
                            @else
                                <x-tabler-alert-triangle class="size-4" />
                            @endif
                            {{ Arr::get($pingBenchmark, 'value').' '.str(Arr::get($pingBenchmark, 'unit')) }}
                        </span>
                    </x-slot>
                @endfilled

                <p class="flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->latestResult?->ping }}</span>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">ms</span>
                </p>
            </x-filament::section>

            <x-filament::section class="col-span-1" icon="tabler-square-percentage" icon-size="sm">
                <x-slot name="heading">
                    {{ __('results.packet_loss') }}
                </x-slot>

                <p class="flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->latestResult?->packet_loss }}</span>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">%</span>
                </p>
            </x-filament::section>
        </div>
    @endfilled
</div>
