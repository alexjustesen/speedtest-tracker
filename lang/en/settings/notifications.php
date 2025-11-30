<?php

return [
    'title' => 'Notifications',
    'label' => 'Notifications',

    // Database notifications
    'database' => 'Database',
    'database_description' => 'Notifications sent to this channel will show up under the 🔔 icon in the header.',
    'database_on_speedtest_run' => 'Notify on every speedtest run',
    'database_on_threshold_failure' => 'Notify on threshold failures',
    'test_database_channel' => 'Test database channel',

    // Mail notifications
    'mail' => 'Mail',
    'recipients' => 'Recipients',
    'mail_on_speedtest_run' => 'Notify on every speedtest run',
    'mail_on_threshold_failure' => 'Notify on threshold failures',
    'test_mail_channel' => 'Test mail channel',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Enable Apprise notifications',
    'apprise_on_speedtest_run' => 'Notify on every speedtest run',
    'apprise_on_threshold_failure' => 'Notify on threshold failures',
    'apprise_sidecar' => 'Apprise Sidecar',
    'apprise_verify_ssl' => 'Verify SSL',
    'apprise_channels' => 'Apprise Channels',
    'apprise_channel_url' => 'Channel URL',
    'apprise_channel_url_placeholder' => 'discord://WebhookID/WebhookToken',
    'apprise_channel_url_helper' => 'Provide the service endpoint URL for notifications.',
    'apprise_documentation' => 'Apprise Documentation',
    'test_apprise_channel' => 'Test Apprise',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'webhook_on_speedtest_run' => 'Notify on every speedtest run',
    'webhook_on_threshold_failure' => 'Notify on threshold failures',
    'test_webhook_channel' => 'Test webhook channel',
    'webhook_hint_description' => 'These are generic webhooks. For payload examples and implementation details, view the documentation.',

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
