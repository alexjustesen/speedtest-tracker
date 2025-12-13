<x-app-layout title="Results">
    <div class="space-y-6 md:space-y-12 dashboard-page">
        <livewire:next-speedtest-banner />

        <livewire:latest-result-stats />

        <div class="grid grid-cols-1 gap-6">
            <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
                <x-tabler-table class="size-5" />
                Results
            </h2>

            <livewire:list-results />
        </div>
    </div>
</x-app-layout>
