<?php

namespace App\Filament\Resources\Speedtests\Pages;

use App\Filament\Resources\Speedtests\Pages\Concerns\MutatesScheduleFormData;
use App\Filament\Resources\Speedtests\SpeedtestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpeedtest extends EditRecord
{
    use MutatesScheduleFormData;

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['server_mode'] = $this->record->server_mode;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applyScheduleFormMutations($data);
    }

    protected function afterSave(): void
    {
        $this->record->syncCronSchedule(
            expression: $this->pendingCronExpression,
            enabled: $this->pendingEnabled,
            timezone: config('app.display_timezone'),
        );
    }
}
