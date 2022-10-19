<?php

namespace App\Filament\Resources\ResultResource\Pages;

use App\Filament\Resources\ResultResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListResults extends ListRecords
{
    protected static string $resource = ResultResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->orderBy('id', 'desc');
    }

    protected function getHeaderWidgets(): array
    {
        return ResultResource::getWidgets();
    }
}
