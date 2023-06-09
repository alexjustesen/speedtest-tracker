<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use App\Settings\GeneralSettings;
use Filament\Resources\Pages\ListRecords;

class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;

    protected function getTablePollingInterval(): ?string
    {
        return config('speedtest.results_polling');
    }

    protected function getMaxContentWidth(): string
    {
        $settings = new GeneralSettings();

        return $settings->content_width;
    }

    protected function getHeaderWidgets(): array
    {
        return ResultResource::getWidgets();
    }
}
