<?php

return [
    'title' => 'Notificaties',
    'label' => 'Notificaties',

    // Database notifications
    'database' => 'Database',
    'database_description' => 'Meldingen die naar dit kanaal worden verzonden worden weergegeven onder de 🔔 icoon in de header.',
    'database_on_speedtest_run' => 'Notificatie bij elke snelheidstest uitgevoerd',
    'database_on_threshold_failure' => 'Melding bij limiet overschrijding',
    'test_database_channel' => 'Test database notificaties',

    // Mail notifications
    'mail' => 'E-mailen',
    'recipients' => 'Ontvangers',
    'mail_on_speedtest_run' => 'Notificatie bij elke snelheidstest uitgevoerd',
    'mail_on_threshold_failure' => 'Melding bij limiet overschrijding',
    'test_mail_channel' => 'Test e-mailkanaal',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'webhook_on_speedtest_run' => 'Notificatie bij elke snelheidstest uitgevoerd',
    'webhook_on_threshold_failure' => 'Melding bij limiet overschrijding',
    'test_webhook_channel' => 'Test webhook kanaal',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificatie bij elke snelheidstest uitgevoerd',
    'notify_on_threshold_failures' => 'Melding bij drempelfouten',

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
