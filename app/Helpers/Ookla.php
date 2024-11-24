<?php

namespace App\Helpers;

use App\Actions\GetOoklaSpeedtestServers;
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

            return ''; // If it's not valid JSON or doesn't contain "message", return an empty string
        }, $messages);

        // Filter out empty messages and concatenate
        $errorMessage = implode(' | ', array_filter($errorMessages));

        return $errorMessage;
    }

    /**
     * Fetches and returns the server list from the GetOoklaSpeedtestServers action.
     */
    public static function GetOoklaSpeedtestServers(): array
    {
        // Call the action to get the servers
        return (new GetOoklaSpeedtestServers)->handle();
    }

    /**
     * Maps the server IDs from the configuration to their names using the GetOoklaSpeedtestServers action.
     */
    public static function getConfigServers(): ?array
    {
        $list = [];

        // Check if servers are configured
        if (blank(config('speedtest.servers'))) {
            return null;
        }

        // Fetch the server list using the GetOoklaSpeedtestServers method
        $servers = self::GetOoklaSpeedtestServers();

        // If no servers are returned, return null
        if (empty($servers)) {
            return null;
        }

        // Get the configured server IDs
        $configuredServers = collect(array_map(
            'trim',
            explode(',', config('speedtest.servers'))
        ));

        // Loop through each configured server ID and match it with the fetched server list
        foreach ($configuredServers as $serverId) {
            // Check if the server exists in the list
            if (isset($servers[$serverId])) {
                // If the server exists, use the formatted sponsor, name, and id
                $list[$serverId] = $servers[$serverId];
            } else {
                // If the server isn't found, show the server ID with a "not available" message
                $list[$serverId] = $serverId.' (Name not available)';
            }
        }

        // Sort the server list alphabetically by formatted name
        return $list ? collect($list)->sort()->toArray() : null;
    }
}
