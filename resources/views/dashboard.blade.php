<x-app-layout title="Dashboard">
    <div class="grid gap-4 sm:grid-cols-6 sm:gap-8">
        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
        </div>

        @isset($latestResult)
            <div class="text-sm font-semibold leading-6 text-center col-span-full sm:text-base">
                Latest result: <time datetime="{{ $latestResult->created_at }}">{{ $diff }}</time>
            </div>
        @endisset

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentDownloadChartWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentUploadChartWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentPingChartWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentJitterChartWidget::class)
        </div>
    </div>
</x-app-layout>
