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

    // General Notification Settings
    'label' => 'Notifications',
    'triggers' => 'Triggers',
    'notify_on_every_speedtest_run' => 'Notify on every speedtest run',
    'notify_on_threshold_failures' => 'Notify on threshold failures',
    'threshold_helper_text' => 'Set thresholds for speedtest results. Leave empty to disable.',
    'topic' => 'Topic',
    'username' => 'Username',
    'username_placeholder' => 'Username (optional)',
    'password' => 'Password',
    'password_placeholder' => 'Password (optional)',
    'user_key' => 'User Key',
    'recipients' => 'Recipients',
    'url' => 'URL',

    // Database Notifications
    'database' => [
        'title' => 'Database',
        'description' => 'Configure database notification settings.',
        'enable' => 'Enable Database Notifications',
        'test' => 'Test Database Channel',
        'received' => 'Test notification received',
        'sent' => 'Test notification sent',
        'ping' => 'Ping!',
        'pong' => 'Pong!',
    ],

    // Discord Notifications
    'discord' => [
        'title' => 'Discord',
        'enable' => 'Enable Discord Webhook Notifications',
        'test' => 'Test Discord Webhook',
        'add' => 'Please add at least one Discord webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Discord.',
        'webhooks' => 'Discord Webhooks',
    ],

    // Gotify Notifications
    'gotfy' => [
        'title' => 'Gotify',
        'enable' => 'Enable Gotify Notifications',
        'test' => 'Test Gotify Webhook',
        'add' => 'Please add at least one Gotify webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Gotify.',
        'webhooks' => 'Gotify Webhooks',

    ],

    // Healthchecks.io Notifications
    'health_check' => [
        'title' => 'Healthchecks.io',
        'enable' => 'Enable Healthchecks.io Notifications',
        'test' => 'Test Healthchecks.io Webhook',
        'helper_text' => 'Threshold notifications will be sent to the /fail path of the URL.',
        'add' => 'Please add at least one Healthchecks.io webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Healthchecks.io.',
        'webhooks' => 'Healthchecks.io Webhooks',
    ],

    // Mail Notifications
    'mail' => [
        'title' => 'Mail',
        'enable' => 'Enable Mail Notifications',
        'test' => 'Test Mail Channel',
        'add' => 'Please add at least one email recipient.',
        'sent' => 'Test email sent successfully.',
        'recipients' => 'Recipients',
        'address' => 'Email Address',
    ],

    // Ntfy Notifications
    'ntfy' => [
        'title' => 'Ntfy',
        'enable' => 'Enable Ntfy Notifications',
        'test' => 'Test Ntfy Webhook',
        'topic' => 'Topic',
        'server_url' => 'Your Ntfy Server URL',
        'topic_url' => 'Your Ntfy Topic',
        'add' => 'Please add at least one Ntfy webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'received' => 'Test notification received',
        'sent' => 'Test notification sent to Ntfy.',
    ],

    // Pushover Notifications
    'pushover' => [
        'title' => 'Pushover',
        'webhooks' => 'Pushover Webhooks',
        'enable' => 'Enable Pushover Notifications',
        'test' => 'Test Pushover Webhook',
        'api_token' => 'Pushover API Token',
        'user_key' => 'Pushover User Key',
        'add' => 'Please add at least one Pushover webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'received' => 'Test notification received',
        'sent' => 'Test notification sent to Pushover.',
        'your_pushover_user_key' => 'your_pushover_user_key',
        'your_pushover_api_token' => 'your_pushover_api_token',
    ],

    // Slack Notifications
    'slack' => [
        'title' => 'Slack',
        'enable' => 'Enable Slack Notifications',
        'test' => 'Test Slack Webhook',
        'add' => 'Please add at least one Slack webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'received' => 'Test notification received',
        'sent' => 'Test notification sent to Slack.',
    ],

    // Telegram Notifications
    'telegram' => [
        'title' => 'Telegram',
        'enable' => 'Enable Telegram Notifications',
        'test' => 'Test Telegram',
        'disable_notification' => 'Disable Notification',
        'add' => 'Please add at least one Telegram recipient.',
        'test_message' => '👋 Testing the Telegram notification channel.',
        'received' => 'Test notification received',
        'sent' => 'Test notification sent to Telegram.',
        'chat_id' => 'Chat ID',
    ],

    // Webhook Notifications
    'webhook' => [
        'title' => 'Webhook',
        'enable' => 'Enable Webhook Notifications',
        'test' => 'Test Webhook Channel',
        'add' => 'Please add at least one webhook.',
        'payload' => 'Speedtest Tracker Test',
        'received' => 'Test notification received',
        'sent' => 'Test webhook sent successfully.',
    ],

];
