<x-app-layout title="Dashboard">
    <div class="grid gap-4 sm:grid-cols-6 sm:gap-8">
        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\StatsOverviewWidget::class)
        </div>

        @isset($latestResult)
            <div class="text-sm font-semibold leading-6 text-center col-span-full sm:text-base">
                Latest result: <time title="{{ $latestResult->created_at->timezone(config('app.display_timezone'))->format(config('app.datetime_format')) }}" datetime="{{ $latestResult->created_at->timezone(config('app.display_timezone')) }}">{{ $latestResult->created_at->diffForHumans() }}</time>
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

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentDownloadLatencyChartWidget::class)
        </div>

        <div class="col-span-full">
            @livewire(\App\Filament\Widgets\RecentUploadLatencyChartWidget::class)
        </div>

    </div>

</x-app-layout>
