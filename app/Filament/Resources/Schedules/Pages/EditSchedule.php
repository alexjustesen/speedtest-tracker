<?php

namespace App\Filament\Resources\Schedules\Pages;

use App\Actions\Schedules\ResolveScheduleServerLabels;
use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSchedule extends EditRecord
{
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
        $serverMode = $data['server_mode'] ?? 'auto';
        unset($data['server_mode']);

        if ($serverMode !== 'prefer') {
            $data['servers'] = null;
        }

        if ($serverMode !== 'block') {
            $data['blocked_servers'] = null;
        }

        $data['server_labels'] = ResolveScheduleServerLabels::run(
            servers: $data['servers'] ?? [],
            blockedServers: $data['blocked_servers'] ?? [],
        );

        return $data;
    }
}
