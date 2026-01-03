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
    'apprise_server_url_helper' => 'Die URL deines Apprise Servers. Die URL muss mit /notify enden',
    'apprise_verify_ssl' => 'SSL verifizieren',
    'apprise_channels' => 'Benachrichtigungskanäle',
    'apprise_channel_url' => 'Service URL',
    'apprise_hint_description' => 'Apprise erlaubt es dir Benachrichtigungen zu 90+ Services zu senden. Du musst einen Apprise Server hosten und folgende Service URLs konfigurieren.',
    'apprise_channel_url_helper' => 'Verwende ein Apprise URL Format. Beispiel: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Speichere deine Einstellungen um die Benachrichtigung zu Testen.',
    'test_apprise_channel' => 'Apprise testen',
    'apprise_channel_url_validation_error' => 'Ungültige Apprise URL. Es muss ein Apprise Format verwendet werden (z.B. discord://, slack://), und nicht http:// oder https://. Für mehr Informationen die Apprise Dokumentation prüfen',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Webhook-Kanal testen',
    'webhook_hint_description' => 'Dies sind generische Webhooks. Payload Beispiele und Implementations-Details s. h. Dokumentation. Für Services wie Discord',

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
