<?php

namespace App\Filament\Resources\Speedtests\Pages\Concerns;

use App\Actions\Schedules\ResolveScheduleServerLabels;

trait MutatesScheduleFormData
{
    protected ?string $pendingCronExpression = null;

    protected bool $pendingEnabled = true;

    protected function applyScheduleFormMutations(array $data): array
    {
        $this->pendingCronExpression = $data['schedule'] ?? null;
        $this->pendingEnabled = (bool) ($data['enabled'] ?? true);
        unset($data['schedule'], $data['enabled']);

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
