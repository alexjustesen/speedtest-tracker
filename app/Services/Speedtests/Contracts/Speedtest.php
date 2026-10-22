<?php

namespace App\Services\Speedtests\Contracts;

interface Speedtest
{
    /**
     * Run the speedtest and return the result data in the same shape as the Ookla CLI output,
     * so the rest of the application (charts, notifications, integrations) can treat it the same way.
     *
     * @throws \RuntimeException when the speedtest could not be completed
     */
    public function run(?string $target = null): array;
}
