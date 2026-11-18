<?php

return [
    'title' => 'Notifications',
    'label' => 'Notifications',
    'channel' => 'Notification channel',
    'channels' => 'Notification channels',

    // Form
    'name' => 'Name',
    'name_placeholder' => 'Discord alerts',
    'type' => 'Type',
    'configuration' => 'Configuration',
    'triggers' => 'Triggers',
    'events' => 'Events',
    'events_helper' => 'Select one or more events this channel should be notified about.',
    'event_groups' => [
        'speedtest' => 'Speedtest',
        'benchmarks' => 'Benchmark',
    ],
    'include_unscheduled' => 'Include manual speedtests',
    'include_unscheduled_helper' => 'By default only scheduled speedtests send notifications.',

    // Table
    'last_sent_at' => 'Last sent',
    'status' => 'Status',
    'never' => 'Never',
    'healthy' => 'Delivering',

    // Mail
    'recipients' => 'Recipients',

    // Apprise
    'optional' => 'Optional',
    'apprise_server_url' => 'Apprise Server URL',
    'apprise_server_url_helper' => 'The URL of your Apprise Server. The URL must end on /notify, or /notify/{key} to use a persistent configuration.',
    'apprise_username' => 'Username',
    'apprise_password' => 'Password',
    'apprise_verify_ssl' => 'Verify SSL',
    'apprise_channels' => 'Service URLs',
    'apprise_channels_helper' => 'Use Apprise URL format. Examples: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_tags' => 'Tags',
    'apprise_tags_helper' => 'Notify the services tagged in a persistent Apprise configuration. Matches any of the tags.',
    'apprise_channel_url_validation_error' => 'Invalid Apprise URL. Must use Apprise format (e.g., discord://, slack://), not http:// or https://. See the Apprise documentation for more information',

    // Webhook
    'webhook_url' => 'URL',
    'webhook_secret' => 'Signing secret',
    'webhook_secret_helper' => 'Optional. When set, the payload is signed and sent in the Signature header.',

    // Test
    'send_test' => 'Send test',
    'test_sent' => 'Test notification sent',
    'test_failed' => 'Test notification failed',
    'test_queued' => 'Test notification queued',
    'test_queued_body' => 'The delivery result will appear in your notifications shortly.',
    'test_title_prefix' => '[Test]',

    // Message titles
    'titles' => [
        'completed' => 'Speedtest Completed - #:id',
        'failed' => 'Speedtest Failed - #:id',
        'recovered' => 'Speedtest Recovered - #:id',
        'benchmark_unhealthy' => 'Speedtest Benchmark Alarm - #:id',
        'benchmark_recovered' => 'Speedtest Benchmarks Recovered - #:id',
    ],

    // Errors
    'errors' => [
        'no_recipients' => 'No mail recipients configured.',
        'no_apprise_server' => 'No Apprise Server URL configured.',
        'no_apprise_targets' => 'Add at least one service URL or tag.',
        'apprise_response' => 'Apprise returned HTTP :status: :error',
        'webhook_failed' => 'Webhook delivery failed (HTTP :status).',
    ],
];
