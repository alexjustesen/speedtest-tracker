<x-app-layout title="Dashboard">
    <div class="space-y-6 md:space-y-12 dashboard-page">
        <livewire:next-speedtest-banner />

        @auth
            <livewire:platform-stats />
        @endauth

        <livewire:latest-result-stats />

        <div class="grid grid-cols-1 gap-6">
            <h2 class="flex items-center gap-x-2 text-base md:text-lg font-semibold text-zinc-900 dark:text-zinc-100 col-span-full">
                <x-tabler-chart-histogram class="size-5" />
                Metrics
            </h2>

            @livewire(\App\Filament\Widgets\RecentDownloadChartWidget::class)

            @livewire(\App\Filament\Widgets\RecentUploadChartWidget::class)

            @livewire(\App\Filament\Widgets\RecentPingChartWidget::class)

            @livewire(\App\Filament\Widgets\RecentJitterChartWidget::class)

            @livewire(\App\Filament\Widgets\RecentDownloadLatencyChartWidget::class)

            @livewire(\App\Filament\Widgets\RecentUploadLatencyChartWidget::class)
        </div>
    </div>
</x-app-layout>
