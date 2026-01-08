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
    'apprise_server_url_helper' => 'The URL of your Apprise Server. The URL must end on /notify',
    'apprise_verify_ssl' => 'Verify SSL',
    'apprise_channels' => 'Notification Channels',
    'apprise_channel_url' => 'Service URL',
    'apprise_hint_description' => 'Apprise allows you to send notifications to 90+ services. You need to run an Apprise server and configure service URLs below.',
    'apprise_channel_url_helper' => 'Use Apprise URL format. Examples: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Save your settings to test the notification.',
    'test_apprise_channel' => 'Test Apprise',
    'apprise_channel_url_validation_error' => 'Invalid Apprise URL. Must use Apprise format (e.g., discord://, slack://), not http:// or https://. See the Apprise documentation for more information',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Test webhook channel',
    'webhook_hint_description' => 'These are generic webhooks. For payload examples and implementation details, view the documentation. For services like Discord, Ntfy etc please use Apprise.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notify on every completed scheduled speedtest run',
    'notify_on_every_speedtest_run_helper' => 'This will send a notification for every completed scheduled speedtest run, only for healthy or unbenchmarked tests',
    'notify_on_threshold_failures' => 'Notify on threshold failures for scheduled speedtests',
    'notify_on_threshold_failures_helper' => 'This will send a notification when a scheduled speedtest fails any configured thresholds',

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
