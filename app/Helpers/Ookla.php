<?php

namespace App\Helpers;

use App\Settings\GeneralSettings;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Ookla
{
    /**
     * Gets the error messages from a failed CLI process exception.
     */
    public static function getErrorMessage(ProcessFailedException $exception): string
    {
        $messages = explode(PHP_EOL, $exception->getMessage());
        $errorMessages = [];

        foreach ($messages as $message) {
            $decoded = json_decode($message, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['message'])) {
                $errorMessages[] = $decoded['message'];
            }
        }

        // If no valid messages, use the placeholder
        if (empty($errorMessages)) {
            $errorMessages[] = 'An unexpected error occurred while running the Ookla CLI.';
        }

        // Remove duplicates and concatenate
        return implode(' | ', array_unique($errorMessages));
    }

    public static function getConfigServers(): ?array
    {
        $list = [];

        if (blank(app(GeneralSettings::class)->speedtest_servers)) {
            return null;
        }

        $servers = collect(array_map(
            'trim',
            explode(',', app(GeneralSettings::class)->speedtest_servers)
        ));

        if (! count($servers)) {
            return null;
        }

        $list = $servers->mapWithKeys(function ($serverId) {
            return [$serverId => $serverId.' (Config server)'];
        })->sort()->toArray();

        return $list;
    }
}
