<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Notification Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for various notification messages
    | that we need to display to the user. You are free to modify these
    | language lines according to your application's requirements.
    |
    */

    // Notification Settings
    'label' => 'Notifications',
    'triggers' => 'Triggers',
    'notify_on_every_speedtest_run' => 'Notify on every speedtest run',
    'notify_on_threshold_failures' => 'Notify on threshold failures',
    'threshold_helper_text' => 'Set thresholds for speedtest results. Leave empty to disable.',

    // Database Notifications
    'database' => 'Database',
    'database_description' => 'Configure database notification settings.',
    'enable_database_notifications' => 'Enable Database Notifications',
    'test_database_channel' => 'Test Database Channel',

    // Email Notifications
    'mail' => 'Mail',
    'enable_mail_notifications' => 'Enable Mail Notifications',
    'recipients' => 'Recipients',
    'email_address' => 'Email Address',
    'test_mail_channel' => 'Test Mail Channel',

    // Webhook Notifications
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'enable_webhook_notifications' => 'Enable Webhook Notifications',
    'test_webhook_channel' => 'Test Webhook Channel',

    // Discord
    'discord' => 'Discord',
    'enable_discord_webhook_notifications' => 'Enable Discord Webhook Notifications',
    'test_discord_webhook' => 'Test Discord Webhook',

    // Gotify
    'gotify' => 'Gotify',
    'gotify_enabled' => 'Enable Gotify Notifications',
    'test_gotify_webhook' => 'Test Gotify Webhook',

    // Healthchecks.io
    'healthcheck_io' => 'Healthchecks.io',
    'healthcheck_enabled' => 'Enable Healthchecks.io Notifications',
    'test_healthcheck_webhook' => 'Test Healthchecks.io Webhook',

    // Ntfy
    'ntfy' => 'Ntfy',
    'ntfy_enabled' => 'Enable Ntfy Notifications',
    'test_ntfy_webhook' => 'Test Ntfy Webhook',
    'your_ntfy_server_url' => 'Your Ntfy Server URL',
    'your_ntfy_topic' => 'Your Ntfy Topic',
    'topic' => 'Topic',
    'username' => 'Username',
    'username_placeholder' => 'Username (optional)',
    'password' => 'Password',
    'password_placeholder' => 'Password (optional)',

    // Pushover
    'pushover' => 'Pushover',
    'pushover_webhooks' => 'Pushover Webhooks',
    'enable_pushover_webhook_notifications' => 'Enable Pushover Notifications',
    'test_pushover_webhook' => 'Test Pushover Webhook',
    'your_pushover_api_token' => 'Your Pushover API Token',
    'your_pushover_user_key' => 'Your Pushover User Key',
    'user_key' => 'User Key',

    // Slack
    'slack' => 'Slack',
    'slack_enabled' => 'Enable Slack Notifications',
    'test_slack_webhook' => 'Test Slack Webhook',

    // Telegram
    'telegram' => 'Telegram',
    'enable_telegram' => 'Enable Telegram Notifications',
    'telegram_disable_notification' => 'Disable Notification',
    'test_telegram_webhook' => 'Test Telegram',


    // Database Notifications
    'database' => [
        'title' => 'Database',
        'received' => 'Test notification received',
        'pong' => 'Pong!',
        'sent' => 'Test notification sent',
        'ping' => 'Ping!',
    ],

    // Discord Notifications
    'discord' => [
        'title' => 'Discord',
        'add' => 'Please add at least one Discord webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Discord.',
    ],

    // Gotify Notifications (Note: typo in code - 'gotfy' instead of 'gotify')
    'gotfy' => [
        'title' => 'Gotify',
        'add' => 'Please add at least one Gotify webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Gotify.',
    ],

    // Health Check Notifications
    'health_check' => [
        'title' => 'Healthchecks.io',
        'helper_text' => 'Threshold notifications will be sent to the /fail path of the URL.',
        'add' => 'Please add at least one Healthchecks.io webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Healthchecks.io.',
    ],

    // Mail Notifications
    'mail' => [
        'title' => 'Mail',
        'add' => 'Please add at least one email recipient.',
        'sent' => 'Test email sent successfully.',
    ],

    // Ntfy Notifications
    'ntfy' => [
        'title' => 'Ntfy',
        'add' => 'Please add at least one Ntfy webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Ntfy.',
    ],

    // Pushover Notifications
    'pushover' => [
        'title' => 'Pushover',
        'add' => 'Please add at least one Pushover webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Pushover.',
    ],

    // Slack Notifications
    'slack' => [
        'title' => 'Slack',
        'add' => 'Please add at least one Slack webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Slack.',
    ],

    // Telegram Notifications
    'telegram' => [
        'title' => 'Telegram',
        'add' => 'Please add at least one Telegram recipient.',
        'sent' => 'Test notification sent to Telegram.',
        'test_message' => '👋 Testing the Telegram notification channel.',
    ],

    // Webhook Notifications
    'webhook' => [
        'title' => 'Webhook',
        'add' => 'Please add at least one webhook.',
        'payload' => 'Speedtest Tracker Test',
        'sent' => 'Test webhook sent successfully.',
    ],

];
