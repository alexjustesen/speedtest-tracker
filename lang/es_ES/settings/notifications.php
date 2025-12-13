<?php

return [
    'title' => 'Notificaciones',
    'label' => 'Notificaciones',

    // Database notifications
    'database' => 'Base de datos',
    'database_description' => 'Las notificaciones enviadas a este canal se mostrarán bajo el icono :belell: en el encabezado.',
    'test_database_channel' => 'Probar canal de base de datos',

    // Mail notifications
    'mail' => 'Correo',
    'recipients' => 'Destinatarios',
    'test_mail_channel' => 'Canal de prueba de correo',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Habilitar notificaciones Apprise',
    'apprise_server' => 'Servidor Apprise',
    'apprise_server_url' => 'URL del servidor',
    'apprise_verify_ssl' => 'Verificar SSL',
    'apprise_channels' => 'Canales de expedición',
    'apprise_channel_url' => 'URL del canal',
    'apprise_hint_description' => 'Para más información sobre cómo configurar Apprise, vea la documentación.',
    'apprise_channel_url_helper' => 'Proporcionar la URL de los puntos finales del servicio para las notificaciones.',
    'test_apprise_channel' => 'Prueba de aviso',
    'apprise_channel_url_validation_error' => 'La URL del canal Apprise no debe comenzar con "http" o "https". Por favor, proporcione un esquema de URL de Apprise válido.',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Probar canal webhook',
    'webhook_hint_description' => 'Estos son webhooks genéricos. Para ejemplos de payload y detalles de la implementación, vea la documentación.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificar en cada prueba de velocidad programada',
    'notify_on_threshold_failures' => 'Notificar fallos de umbral para pruebas de velocidad programadas',

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
