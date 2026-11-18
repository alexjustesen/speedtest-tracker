<?php

namespace App\Filament\Resources\Speedtests\Pages;

use App\Filament\Resources\Speedtests\SpeedtestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpeedtest extends EditRecord
{
    protected static string $resource = SpeedtestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
