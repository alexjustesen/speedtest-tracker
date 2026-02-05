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

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Activer les notifications de base de données',
    'apprise_server' => 'Serveur Apprise',
    'apprise_server_url' => 'Serveur Apprise',
    'apprise_server_url_helper' => 'L\'URL de votre serveur Apprise. L\'URL doit se terminer le /notifier',
    'apprise_verify_ssl' => 'Vérifier SSL',
    'apprise_channels' => 'Chaînes de notification',
    'apprise_channel_url' => 'URL du service',
    'apprise_hint_description' => 'Apprise vous permet d\'envoyer des notifications à plus de 90 services. Vous devez exécuter un serveur Apprise et configurer les URL de service ci-dessous.',
    'apprise_channel_url_helper' => 'Utiliser le format d\'URL d\'Apprise. Exemples: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Enregistrez vos paramètres pour tester la notification.',
    'test_apprise_channel' => 'Apprise de test',
    'apprise_channel_url_validation_error' => 'URL d\'Apprise invalide. Doit utiliser le format Apprise (par exemple, discord://, SlackHTTPSHTTPSHTTPSHTTPS',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Tester le canal webhook',
    'webhook_hint_description' => 'Ce sont des webhooks génériques. Pour des exemples de charge utile et des détails d\'implémentation, consultez la documentation. Pour les services comme Discord, Nef',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notifier à chaque exécution de test de vitesse planifiée terminée',
    'notify_on_every_speedtest_run_helper' => 'Ceci enverra une notification pour chaque test de vitesse planifié terminé, uniquement pour les tests sains ou non comparés',
    'notify_on_threshold_failures' => 'Notifier les pannes de seuil pour les tests de vitesse programmés',
    'notify_on_threshold_failures_helper' => 'Ceci enverra une notification lorsqu\'un test de vitesse programmé échoue à tous les seuils configurés',

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
