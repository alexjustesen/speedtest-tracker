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

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Apprise Benachrichtigungen aktivieren',
    'apprise_server' => 'Apprise Server',
    'apprise_server_url' => 'Apprise Server URL',
    'apprise_verify_ssl' => 'SSL verifizieren',
    'apprise_channels' => 'Apprise Kanäle',
    'apprise_channel_url' => 'Kanal URL',
    'apprise_hint_description' => 'Lesen Sie für weitere Informationen zum Einrichten von Apprise die Dokumentation.',
    'apprise_channel_url_helper' => 'Geben Sie die Service Endpoint URL für Benachrichtigung an.',
    'test_apprise_channel' => 'Apprise testen',
    'apprise_channel_url_validation_error' => 'Die Apprise Channel URL muss nicht mit "HTTP" oder "HTTPS" starten. Geben Sie ein valides Apprise URL Schema an.',

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
