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
            Card::make('Latest download', formatBits(formatBytesToBits(Result::latest()->first()?->download ?: 0)).'ps'),
            Card::make('Latest upload', formatBits(formatBytesToBits(Result::latest()->first()?->upload ?: 0)).'ps'),
            Card::make('Latest ping', round(Result::latest()->first()?->ping ?: 0, 2)),
        ];
    }
}
