<?php

return [
    'title' => 'Benachrichtigungen',
    'label' => 'Benachrichtigungen',

    // Database notifications
    'database' => 'Datenbank',
    'database_description' => 'Benachrichtigungen, die an diesen Kanal gesendet werden, werden unter 🔔 Symbol in der Kopfzeile angezeigt.',
    'database_on_speedtest_run' => 'Bei jedem Schnelltest benachrichtigen',
    'database_on_threshold_failure' => 'Benachrichtigen bei Schwellenausfällen',
    'test_database_channel' => 'Datenbankkanal testen',

    // Mail notifications
    'mail' => 'Mail',
    'recipients' => 'Empfänger',
    'mail_on_speedtest_run' => 'Bei jedem Schnelltest benachrichtigen',
    'mail_on_threshold_failure' => 'Benachrichtigen bei Schwellenausfällen',
    'test_mail_channel' => 'Mail-Kanal testen',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'webhook_on_speedtest_run' => 'Bei jedem Schnelltest benachrichtigen',
    'webhook_on_threshold_failure' => 'Benachrichtigen bei Schwellenausfällen',
    'test_webhook_channel' => 'Webhook-Kanal testen',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Bei jedem Schnelltest benachrichtigen',
    'notify_on_threshold_failures' => 'Benachrichtigen bei Schwellenausfällen',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Ich sage: Ping',
            'pong' => 'Sie sagen: Pong',
            'received' => 'Testdatenbank-Benachrichtigung erhalten!',
            'sent' => 'Testdatenbank-Benachrichtigung gesendet.',
        ],
        'mail' => [
            'add' => 'E-Mail-Empfänger hinzufügen!',
            'sent' => 'Test-E-Mail-Benachrichtigung gesendet.',
        ],
        'webhook' => [
            'add' => 'Webhook URLs hinzufügen!',
            'sent' => 'Webhook Benachrichtigung gesendet.',
            'payload' => 'Teste Webhook-Benachrichtigung',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Grenzwert-Benachrichtigungen werden an die /fail Route in der URL gesendet.',
];
