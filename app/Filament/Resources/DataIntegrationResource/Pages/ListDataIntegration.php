<?php

namespace App\Filament\Resources\DataIntegrationResource\Pages;

use App\Filament\Resources\DataIntegrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataIntegration extends ListRecords
{
    protected static string $resource = DataIntegrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
