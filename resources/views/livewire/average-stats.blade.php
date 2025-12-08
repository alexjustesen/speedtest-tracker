<div wire:poll.60s>
    @if($this->results->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 average-stats">
            <div class="col-span-full">
                <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                    <x-tabler-chart-line class="size-5" />
                    {{ __('general.average') }}
                </h2>
            </div>

            <x-filament::section class="col-span-1" icon="tabler-download" icon-size="md">
                <x-slot name="heading">
                    {{ __('general.download') }}
                </x-slot>

                <p class="flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->formattedDownload[0] }}</span>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $this->formattedDownload[1].'ps' }}</span>
                </p>
            </x-filament::section>

            <x-filament::section class="col-span-1" icon="tabler-upload" icon-size="md">
                <x-slot name="heading">
                    {{ __('general.upload') }}
                </x-slot>

                <p class="flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->formattedUpload[0] }}</span>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $this->formattedUpload[1].'ps' }}</span>
                </p>
            </x-filament::section>

            <x-filament::section class="col-span-1" icon="tabler-clock-bolt" icon-size="sm">
                <x-slot name="heading">
                    {{ __('general.ping') }}
                </x-slot>

                <p class="flex items-baseline gap-x-2">
                    <span class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->averagePing }}</span>
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">ms</span>
                </p>
            </x-filament::section>
        </div>
    @endif
</div>
