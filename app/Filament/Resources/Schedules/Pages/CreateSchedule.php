<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\Pages\Concerns\MutatesScheduleFormData;
use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    use MutatesScheduleFormData;

    protected static string $resource = ScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applyScheduleFormMutations($data);
    }
}
