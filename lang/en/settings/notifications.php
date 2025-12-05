<?php

return [
    'title' => 'Notifications',
    'label' => 'Notifications',

    // Database notifications
    'database' => 'Database',
    'database_description' => 'Notifications sent to this channel will show up under the 🔔 icon in the header.',
    'test_database_channel' => 'Test database channel',

    // Mail notifications
    'mail' => 'Mail',
    'recipients' => 'Recipients',
    'test_mail_channel' => 'Test mail channel',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Enable Apprise notifications',
    'apprise_server' => 'Apprise Server',
    'apprise_server_url' => 'Apprise Server URL',
    'apprise_verify_ssl' => 'Verify SSL',
    'apprise_channels' => 'Apprise Channels',
    'apprise_channel_url' => 'Channel URL',
    'apprise_hint_description' => 'For more information on setting up Apprise, view the documentation.',
    'apprise_channel_url_helper' => 'Provide the service endpoint URL for notifications.',
    'test_apprise_channel' => 'Test Apprise',
    'apprise_channel_url_validation_error' => 'The Apprise channel URL must not start with "http" or "https". Please provide a valid Apprise URL scheme.',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Test webhook channel',
    'webhook_hint_description' => 'These are generic webhooks. For payload examples and implementation details, view the documentation.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notify on every scheduled speedtest run',
    'notify_on_threshold_failures' => 'Notify on threshold failures for scheduled speedtests',

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
