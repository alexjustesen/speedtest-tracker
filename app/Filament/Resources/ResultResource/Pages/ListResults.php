<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\ListRecords;

class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;

    protected function getTablePollingInterval(): ?string
    {
        return config('speedtest.results_polling');
    }

    protected function getHeaderWidgets(): array
    {
        return ResultResource::getWidgets();
    }
}
