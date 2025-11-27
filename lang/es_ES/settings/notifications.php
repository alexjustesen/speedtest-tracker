<?php

return [
    'title' => 'Notificaciones',
    'label' => 'Notificaciones',

    // Database notifications
    'database' => 'Base de datos',
    'database_description' => 'Las notificaciones enviadas a este canal se mostrarán bajo el icono :belell: en el encabezado.',
    'database_on_speedtest_run' => 'Notificar en cada prueba de velocidad',
    'database_on_threshold_failure' => 'Notificar en los umbrales de fallos',
    'test_database_channel' => 'Probar canal de base de datos',

    // Mail notifications
    'mail' => 'Correo',
    'recipients' => 'Destinatarios',
    'mail_on_speedtest_run' => 'Notificar en cada prueba de velocidad',
    'mail_on_threshold_failure' => 'Notificar en los umbrales de fallos',
    'test_mail_channel' => 'Canal de prueba de correo',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'webhook_on_speedtest_run' => 'Notificar en cada prueba de velocidad',
    'webhook_on_threshold_failure' => 'Notificar en los umbrales de fallos',
    'test_webhook_channel' => 'Probar canal webhook',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificar en cada prueba de velocidad',
    'notify_on_threshold_failures' => 'Notificar en los umbrales de fallos',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Yo digo: ping',
            'pong' => 'Dice usted: pong',
            'received' => 'Notificación de la base de datos de prueba recibida!',
            'sent' => 'Notificación de prueba de base de datos enviada.',
        ],
        'mail' => [
            'add' => '¡Añadir destinatarios de correo!',
            'sent' => 'Notificación de correo de prueba enviada.',
        ],
        'webhook' => [
            'add' => '¡Añadir URL de webhook!',
            'sent' => 'Prueba de notificación de webhook enviada.',
            'payload' => 'Probando notificación de webhook',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Las notificaciones de umbral se enviarán a la ruta /fail en la URL.',
];
