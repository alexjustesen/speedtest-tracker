<?php

return [
    'title' => 'Notificaties',
    'label' => 'Notificaties',

    // Database notifications
    'database' => 'Database',
    'database_description' => 'Meldingen die naar dit kanaal worden verzonden worden weergegeven onder de 🔔 icoon in de header.',
    'test_database_channel' => 'Test database notificaties',

    // Mail notifications
    'mail' => 'E-mailen',
    'recipients' => 'Ontvangers',
    'test_mail_channel' => 'Test e-mailkanaal',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Inschakelen Apprise meldingen',
    'apprise_server' => 'Apprise Server',
    'apprise_server_url' => 'Appprise Server-URL',
    'apprise_verify_ssl' => 'Controleer SSL',
    'apprise_channels' => 'Apprise Kanalen',
    'apprise_channel_url' => 'Kanaal URL',
    'apprise_hint_description' => 'Voor meer informatie over het instellen van Apprise, bekijk de documentatie.',
    'apprise_channel_url_helper' => 'Geef de service eindpunt URL voor meldingen.',
    'test_apprise_channel' => 'Test Apprise',
    'apprise_channel_url_validation_error' => 'De URL van het Apprise kanaal mag niet beginnen met "http" of "https". Geef een geldig URL-schema op.',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Test webhook kanaal',
    'webhook_hint_description' => 'Dit zijn generieke webhooks. Raadpleeg de documentatie voor voorbeelden van payloads en implementatiedetails.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificatie bij elke geplande snelheidstest',
    'notify_on_threshold_failures' => 'Melding bij drempelfouten voor geplande snelheidstests',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Ik zeg: ping',
            'pong' => 'Jij zegt: pong',
            'received' => 'Test database melding ontvangen!',
            'sent' => 'Test database melding verzonden.',
        ],
        'mail' => [
            'add' => 'Ontvangers toevoegen!',
            'sent' => 'Test mail melding verzonden.',
        ],
        'webhook' => [
            'add' => 'Voeg webhook URL\'s toe!',
            'sent' => 'Test webhook melding verzonden.',
            'payload' => 'Webhook melding',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Drempel meldingen worden verzonden naar de /fail route in de URL.',
];
