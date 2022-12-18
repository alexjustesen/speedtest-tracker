<?php

namespace App\Filament\Widgets;

use App\Models\Result;
use App\Settings\GeneralSettings;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $result = Result::latest()->first();

        $settings = new GeneralSettings();

        return [
            Card::make('Latest download', fn (): string => ! blank($result) ? formatBits(formatBytesToBits($result->download)).'ps' : 'n/a')
                ->description(! blank($result) ? 'Tested at: '.$result->created_at->timezone($settings->timezone)->format($settings->time_format) : 'No tests')
                ->icon('heroicon-o-download'),
            Card::make('Latest upload', fn (): string => ! blank($result) ? formatBits(formatBytesToBits($result->upload)).'ps' : 'n/a')
                ->icon('heroicon-o-upload'),
            Card::make('Latest ping', fn (): string => ! blank($result) ? round($result->ping, 2).'ms' : 'n/a')
                ->icon('heroicon-o-clock'),
        ];
    }
}
