<div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['total'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1">
            <x-slot name="heading">
                Total successful tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['completed'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1">
            <x-slot name="heading">
                Total failed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['failed'] }}</p>
        </x-filament::section>
    </div>
</div>
