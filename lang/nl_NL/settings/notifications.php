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
