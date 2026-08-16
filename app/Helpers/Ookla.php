<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
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

    /**
     * Clamps negative metric values the CLI sometimes returns to zero.
     */
    public static function clampNegativeValues(?array $output): ?array
    {
        if ($output === null) {
            return null;
        }

        $paths = [
            'ping.latency',
            'download.bandwidth',
            'download.bytes',
            'upload.bandwidth',
            'upload.bytes',
        ];

        foreach ($paths as $path) {
            $value = Arr::get($output, $path);

            if (is_numeric($value) && $value < 0) {
                Arr::set($output, $path, 0);
            }
        }

        return $output;
    }

    public static function getConfigServers(): ?array
    {
        $list = [];

        if (blank(config('speedtest.servers'))) {
            return null;
        }

        $servers = collect(array_map(
            'trim',
            explode(',', config('speedtest.servers'))
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
