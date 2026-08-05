<?php

namespace App\Actions;

use App\Enums\ResultService;
use Lorisleiva\Actions\Concerns\AsAction;

class DispatchSpeedtest
{
    use AsAction;

    /**
     * Runs a speedtest with the configured service.
     */
    public function handle(bool $scheduled = false, ?int $serverId = null, ?int $dispatchedBy = null): mixed
    {
        if (self::service() === ResultService::Nettest) {
            return Nettest\RunSpeedtest::run(
                scheduled: $scheduled,
                serverId: $serverId,
                dispatchedBy: $dispatchedBy,
            );
        }

        return Ookla\RunSpeedtest::run(
            scheduled: $scheduled,
            serverId: $serverId,
            dispatchedBy: $dispatchedBy,
        );
    }

    /**
     * Gets the configured service, falling back to Ookla.
     */
    public static function service(): ResultService
    {
        return ResultService::tryFrom((string) config('speedtest.service')) ?? ResultService::Ookla;
    }
}
