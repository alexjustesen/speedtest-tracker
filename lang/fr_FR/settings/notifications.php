<?php

return [
    'title' => 'Notifications',
    'label' => 'Notifications',

    // Database notifications
    'database' => 'Base de données',
    'database_description' => 'Les notifications envoyées à ce salon apparaîtront sous l\'icône 🔔 dans l\'entête.',
    'test_database_channel' => 'Tester le canal de base de données',

    // Mail notifications
    'mail' => 'Courrier',
    'recipients' => 'Destinataires',
    'test_mail_channel' => 'Tester le canal de messagerie',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Tester le canal webhook',
    'webhook_hint_description' => 'Ce sont des webhooks génériques. Pour des exemples de charge utile et des détails d\'implémentation, consultez la documentation.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notifier à chaque test de vitesse programmé',
    'notify_on_threshold_failures' => 'Notifier les pannes de seuil pour les tests de vitesse programmés',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Je dis: ping',
            'pong' => 'Vous dites: pong',
            'received' => 'Notification de base de données de test reçue !',
            'sent' => 'Notification de base de données de test envoyée.',
        ],
        'mail' => [
            'add' => 'Ajouter des destinataires d\'e-mail!',
            'sent' => 'Notification de test envoyée par e-mail.',
        ],
        'webhook' => [
            'add' => 'Ajouter des URL de webhook !',
            'sent' => 'Notification de test du webhook envoyée.',
            'payload' => 'Test de la notification de webhook',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Les notifications de seuil seront envoyées à la route /fail dans l\'URL.',
];
