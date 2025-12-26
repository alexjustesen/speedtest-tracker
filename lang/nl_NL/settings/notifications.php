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
    'apprise_server_url_helper' => 'De URL van uw Apprise Server. De URL moet eindigen op /notify',
    'apprise_verify_ssl' => 'Controleer SSL',
    'apprise_channels' => 'Notificatie kanalen',
    'apprise_channel_url' => 'Service URL',
    'apprise_hint_description' => 'Met Apprise kan je meldingen verzenden naar meer dan 90 diensten. Je moet een Apprise server hebben draaien en onderstaande service URL\'s configureren.',
    'apprise_channel_url_helper' => 'Gebruik Apprise URL formaat. Bijvoorbeeld discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'test_apprise_channel' => 'Test Apprise',
    'apprise_channel_url_validation_error' => 'Ongeldige Apprise URL. De URL moet gebruik maken van Apprise formaat (bijv. discord://, slack://), niet http:// of https://. Zie de Apprise documentatie voor meer informatie',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Test webhook kanaal',
    'webhook_hint_description' => 'Dit zijn algemene webhooks. Voor payload voorbeelden en implementatiegegevens, bekijk de documentatie. Voor diensten zoals Discord, Ntfy etc. gebruik Apprise.',

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
