<x-filament-panels::page>
    <div wire:poll.5000ms>
        <p class="text-center text-sm">Last speedtest run at: <strong>{{ $lastResult }}</strong></p>
    </div>
</x-filament-panels::page>
