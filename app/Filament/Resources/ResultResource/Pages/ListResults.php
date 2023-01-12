<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }

    protected function getHeaderWidgets(): array
    {
        return ResultResource::getWidgets();
    }
}
