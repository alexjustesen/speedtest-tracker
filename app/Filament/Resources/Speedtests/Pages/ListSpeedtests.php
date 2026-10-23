<?php

namespace App\Filament\Resources\Speedtests\Pages;

use App\Filament\Resources\Speedtests\SpeedtestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpeedtests extends ListRecords
{
    protected static string $resource = SpeedtestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
