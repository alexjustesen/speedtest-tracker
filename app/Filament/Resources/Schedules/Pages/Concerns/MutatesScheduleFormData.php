<?php

namespace App\Filament\Resources\Schedules\Pages\Concerns;

use App\Actions\Schedules\ResolveScheduleServerLabels;

trait MutatesScheduleFormData
{
    protected function applyScheduleFormMutations(array $data): array
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
