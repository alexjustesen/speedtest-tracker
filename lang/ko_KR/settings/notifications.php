<?php

return [
    'title' => 'Notifications',
    'label' => 'Notifications',

    // Database notifications
    'database' => 'Database',
    'database_description' => 'Notifications sent to this channel will show up under the 🔔 icon in the header.',
    'enable_database_notifications' => 'Enable database notifications',
    'database_on_speedtest_run' => 'Notify on every speedtest run',
    'database_on_threshold_failure' => 'Notify on threshold failures',
    'test_database_channel' => 'Test database channel',

    // Mail notifications
    'mail' => 'Mail',
    'enable_mail_notifications' => 'Enable mail notifications',
    'recipients' => 'Recipients',
    'mail_on_speedtest_run' => 'Notify on every speedtest run',
    'mail_on_threshold_failure' => 'Notify on threshold failures',
    'test_mail_channel' => 'Test mail channel',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'enable_webhook_notifications' => 'Enable webhook notifications',
    'webhook_on_speedtest_run' => 'Notify on every speedtest run',
    'webhook_on_threshold_failure' => 'Notify on threshold failures',
    'test_webhook_channel' => 'Test webhook channel',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notify on every speedtest run',
    'notify_on_threshold_failures' => 'Notify on threshold failures',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'I say: ping',
            'pong' => 'You say: pong',
            'received' => 'Test database notification received!',
            'sent' => 'Test database notification sent.',
        ],
        'mail' => [
            'add' => 'Add email recipients!',
            'sent' => 'Test mail notification sent.',
        ],
        'webhook' => [
            'add' => 'Add webhook URLs!',
            'sent' => 'Test webhook notification sent.',
            'payload' => 'Testing webhook notification',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Threshold notifications will be sent to the /fail route in the URL.',
];
