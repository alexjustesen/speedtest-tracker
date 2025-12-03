<?php

return [
    'title' => 'Benachrichtigungen',
    'label' => 'Benachrichtigungen',

    // Database notifications
    'database' => 'Datenbank',
    'database_description' => 'Benachrichtigungen, die an diesen Kanal gesendet werden, werden unter 🔔 Symbol in der Kopfzeile angezeigt.',
    'test_database_channel' => 'Datenbankkanal testen',

    // Mail notifications
    'mail' => 'Mail',
    'recipients' => 'Empfänger',
    'test_mail_channel' => 'Mail-Kanal testen',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Webhook-Kanal testen',
    'webhook_hint_description' => 'Dies sind allgemeine Webhooks. Für Payload-Beispiele und Implementierungsdetails lesen Sie die Dokumentation.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Benachrichtigung bei jedem geplanten Geschwindigkeitstest',
    'notify_on_threshold_failures' => 'Benachrichtigung bei Schwellenausfällen für geplante Geschwindigkeitstests',

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
