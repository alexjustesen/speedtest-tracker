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
    'apprise_server_url_helper' => 'La URL de su Servidor Apprise. La URL debe terminar en /notify',
    'apprise_verify_ssl' => 'Verificar SSL',
    'apprise_channels' => 'Canales de notificación',
    'apprise_channel_url' => 'URL del servicio',
    'apprise_hint_description' => 'Apprise le permite enviar notificaciones a más de 90 servicios. Necesita ejecutar un servidor Apprise y configurar las URL del servicio a continuación.',
    'apprise_channel_url_helper' => 'Usar formato de URL de Apprise. Ejemplos: Discord',
    'apprise_save_to_test' => 'Guarda tus ajustes para probar la notificación.',
    'test_apprise_channel' => 'Prueba de aviso',
    'apprise_channel_url_validation_error' => 'URL no válida. Debe utilizar el formato Apprise (por ejemplo, discord://, slack://), no http:// o https://. Vea la documentación de Apprise para más información',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Probar canal webhook',
    'webhook_hint_description' => 'Estos son webhooks genéricos. Para ejemplos de payload y detalles de la implementación, vea la documentación. Para servicios como Discord, Ntfy etc por favor use Apprise.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificar en cada prueba de velocidad programada completada',
    'notify_on_every_speedtest_run_helper' => 'Esto enviará una notificación para cada prueba de velocidad programada completada, sólo para pruebas saludables o no benchmark',
    'notify_on_threshold_failures' => 'Notificar fallos de umbral para pruebas de velocidad programadas',
    'notify_on_threshold_failures_helper' => 'Esto enviará una notificación cuando una prueba de velocidad programada falle cualquier umbral configurado',

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
