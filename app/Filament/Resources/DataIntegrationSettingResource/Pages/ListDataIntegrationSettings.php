<?php

namespace App\Filament\Resources\DataIntegrationSettingResource\Pages;

use App\Filament\Resources\DataIntegrationSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataIntegrationSettings extends ListRecords
{
    protected static string $resource = DataIntegrationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
