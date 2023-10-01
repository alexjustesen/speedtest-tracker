<x-app-layout title="Dashboard">
    <div class="grid gap-4 sm:grid-cols-6 sm:gap-8">
        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentSpeedChartWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentPingChartWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentJitterChartWidget::class)
        </div>
    </div>
</x-app-layout>
