<?php

return [
    'server_error' => 'Server Error',
    'oops_server_error' => 'Oops, server error!',
    'error_message' => 'Error message',
    'error_fetching_servers' => 'Error fetching servers',
    'servers_refreshed_successfully' => 'Servers refreshed successfully',
    'copied_to_clipboard' => 'Copied to clipboard',

    // Speedtest specific errors
    'ookla_error' => 'An error occurred when listing speedtest servers, check the logs.',
    'cron_invalid' => 'Invalid cron expression',
    'ip_or_cidr_invalid' => 'Each entry must be a valid IP address or CIDR range.',

    // Status fix command
    'status_fix' => [
        'confirm' => 'Do you wish to continue?',
        'fail' => 'Command aborted.',
        'finished' => '✅ done!',
        'info_1' => 'This will check all results and fix the status to "completed" or "failed" based on the data.',
        'info_2' => '📖 Read the documentation: https://docs.speedtest-tracker.dev/other/commands',
    ],
];
