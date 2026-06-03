<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\Pages\Concerns\MutatesScheduleFormData;
use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
    use MutatesScheduleFormData;

    protected static string $resource = ScheduleResource::class;

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->applyScheduleFormMutations($data);
    }
}
