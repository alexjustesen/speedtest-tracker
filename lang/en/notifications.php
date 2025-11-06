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

    // Database Notifications
    'database' => [
        'received' => 'Test notification received',
        'pong' => 'Pong!',
        'sent' => 'Test notification sent',
        'ping' => 'Ping!',
    ],

    // Discord Notifications
    'discord' => [
        'add' => 'Please add at least one Discord webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Discord.',
    ],

    // Gotify Notifications (Note: typo in code - 'gotfy' instead of 'gotify')
    'gotfy' => [
        'add' => 'Please add at least one Gotify webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Gotify.',
    ],

    // Health Check Notifications
    'health_check' => [
        'add' => 'Please add at least one Healthchecks.io webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Healthchecks.io.',
    ],

    // Mail Notifications
    'mail' => [
        'add' => 'Please add at least one email recipient.',
        'sent' => 'Test email sent successfully.',
    ],

    // Ntfy Notifications
    'ntfy' => [
        'add' => 'Please add at least one Ntfy webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Ntfy.',
    ],

    // Pushover Notifications
    'pushover' => [
        'add' => 'Please add at least one Pushover webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Pushover.',
    ],

    // Slack Notifications
    'slack' => [
        'add' => 'Please add at least one Slack webhook.',
        'payload' => 'This is a test notification from Speedtest Tracker.',
        'sent' => 'Test notification sent to Slack.',
    ],

    // Telegram Notifications
    'telegram' => [
        'add' => 'Please add at least one Telegram recipient.',
        'sent' => 'Test notification sent to Telegram.',
        'test_message' => '👋 Testing the Telegram notification channel.',
    ],

    // Webhook Notifications
    'webhook' => [
        'add' => 'Please add at least one webhook.',
        'payload' => 'Speedtest Tracker Test',
        'sent' => 'Test webhook sent successfully.',
    ],

];
