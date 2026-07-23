<?php

namespace App\Filament\Resources\Speedtests\Pages;

use App\Filament\Resources\Speedtests\Pages\Concerns\MutatesScheduleFormData;
use App\Filament\Resources\Speedtests\SpeedtestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpeedtest extends CreateRecord
{
    use MutatesScheduleFormData;

    protected static string $resource = SpeedtestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->applyScheduleFormMutations($data);
    }

    protected function afterCreate(): void
    {
        $this->record->syncCronSchedule(
            expression: $this->pendingCronExpression,
            enabled: $this->pendingEnabled,
            timezone: config('app.display_timezone'),
        );
    }
}
