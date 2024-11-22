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

            return ''; // If it's not valid JSON or doesn't contain "message", return an empty string
        }, $messages);

        // Filter out empty messages and concatenate
        $errorMessage = implode(' | ', array_filter($errorMessages));

        return $errorMessage;
    }
}
