<div wire:poll.60s>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
            <x-tabler-chart-bar class="size-5" />
            {{ __('general.statistics') }}
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

        <x-filament::section class="col-span-1" icon="tabler-hash">
            <x-slot name="heading">
                Total tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['total'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1" icon="tabler-circle-check">
            <x-slot name="heading">
                Total completed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['completed'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1" icon="tabler-alert-circle">
            <x-slot name="heading">
                Total failed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['failed'] }}</p>
        </x-filament::section>
    </div>
</div>
