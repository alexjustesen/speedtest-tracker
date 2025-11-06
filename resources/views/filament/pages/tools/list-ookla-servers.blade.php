<x-filament-panels::page>
    @if ($isLoading)
        <div class="flex items-center justify-center p-12">
            <div class="flex flex-col items-center gap-4">
                <x-filament::loading-indicator class="h-8 w-8" />
                <p class="text-sm text-gray-600 dark:text-gray-400">Loading Ookla servers...</p>
            </div>
        </div>
    @else
        <form wire:submit.prevent>
            {{ $this->form }}
        </form>
    @endif
</x-filament-panels::page>
