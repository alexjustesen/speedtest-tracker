<?php

namespace App\Filament\Pages;

use App\Enums\ResultStatus;
use App\Models\Result;
use Carbon\Carbon;
use Cron\CronExpression;
use Filament\Pages\Dashboard as BasePage;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;

class Dashboard extends BasePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.dashboard';

    public function getTitle(): string
    {
        return __('dashboard.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.title');
    }

    #[Computed()]
    public function resultsStats(): array
    {
        $totalResults = Result::count();
        $completedResults = Result::where('status', ResultStatus::Completed)->count();
        $failedResults = Result::where('status', ResultStatus::Failed)->count();

        return [
            'total' => Number::format($totalResults),
            'completed' => Number::format($completedResults),
            'failed' => Number::format($failedResults),
        ];
    }

    #[Computed()]
    public function latestResult(): ?Result
    {
        return Result::where('status', ResultStatus::Completed)
            ->latest()
            ->first();
    }

    #[Computed()]
    public function nextSpeedtest(): ?Carbon
    {
        if ($schedule = config('speedtest.schedule')) {
            $cronExpression = new CronExpression($schedule);

            return Carbon::parse(time: $cronExpression->getNextRunDate(timeZone: config('app.display_timezone')));
        }

        return null;
    }
}
