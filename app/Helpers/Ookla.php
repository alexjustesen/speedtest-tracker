<?php

namespace App\Helpers;

use Symfony\Component\Process\Exception\ProcessFailedException;

class Ookla
{
    /**
     * Gets the error messages from a failed CLI process exception.
     */
    public static function getErrorMessage(ProcessFailedException $exception): string
    {
        $messages = explode(PHP_EOL, $exception->getMessage());

        // Extract only the "message" part from each JSON error message
        $errorMessages = array_map(function ($message) {
            $decoded = json_decode($message, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['message'])) {
                return $decoded['message'];
            }

            // Placeholder for invalid JSON or missing "message"
            return 'An unexpected error occurred while running the Ookla CLI.';
        }, $messages);

        // Filter out empty messages and concatenate
        $errorMessage = implode(' | ', array_filter($errorMessages));

        return $errorMessage;
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
