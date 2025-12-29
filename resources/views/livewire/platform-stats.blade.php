<div wire:poll.60s>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
            <x-tabler-chart-bar class="size-5" />
            {{ __('general.statistics') }}
        </h2>

        <x-filament::section class="col-span-1" icon="tabler-hash" icon-size="md" :compact="true">
            <x-slot name="heading">
                Total tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['total'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1" icon="tabler-circle-check" icon-size="md" :compact="true">
            <x-slot name="heading">
                Total completed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['completed'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1" icon="tabler-alert-circle" icon-size="md" :compact="true">
            <x-slot name="heading">
                Total failed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['failed'] }}</p>
        </x-filament::section>
    </div>
</div>
