<?php

namespace App\Filament\Resources\ResultResource\Widgets;

use App\Models\Result;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getCards(): array
    {
        return [
            Card::make('Latest download', formatBytes(Result::latest()->first()->download)),
            Card::make('Latest upload', formatBytes(Result::latest()->first()->upload)),
            Card::make('Latest ping', round(Result::latest()->first()->ping, 2)),
        ];
    }
}
