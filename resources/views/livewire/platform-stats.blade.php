<div wire:poll.60s>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
            <x-tabler-chart-bar class="size-5" />
            {{ __('general.statistics') }}
        </h2>

        @filled($this->nextSpeedtest)
            <x-filament::section class="col-span-1">
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
                Total completed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['completed'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-1">
            <x-slot name="heading">
                Total failed tests
            </x-slot>

            <p class="text-xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">{{ $this->platformStats['failed'] }}</p>
        </x-filament::section>

        <x-filament::section class="col-span-full" icon="tabler-chart-pie" icon-size="md">
            <x-slot name="heading">
                Bandwidth Quota
            </x-slot>

            <x-slot name="description">
                Resets on {{ today()->addMonth()->startOfMonth()->format('M. jS, Y') }}
            </x-slot>

            @auth
                <x-slot name="afterHeader">
                    <x-filament::button
                        href="#"
                        tag="a"
                        size="sm"
                    >
                        {{ __('general.edit') }}
                    </x-filament::button>
                </x-slot>
            @endauth

            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-body">Usage</span>
                    <span class="text-sm font-medium text-body">{{ Number::fileSize($this->platformStats['bandwidth_used']['total_bytes'] ?? 0, 2) }} of {{ $this->platformStats['bandwidth_limit'] ?? 'Unlimited' }}</span>
                </div>

                @php
                    $used = 0;

                    if (isset($this->platformStats['bandwidth_used']['total_bytes']) && isset($this->platformStats['bandwidth_limit'])) {
                        $used = round($this->platformStats['bandwidth_used']['total_bytes'] / (\App\Helpers\FileSize::toBytes($this->platformStats['bandwidth_limit']) ?: 1) * 100);
                    }
                @endphp

                <div class="w-full bg-zinc-200 rounded-full h-2">
                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ min($used, 100) }}%" title="{{ number_format($used) }}%"></div>
                </div>
            </div>
        </x-filament::section>
    </div>
</div>
