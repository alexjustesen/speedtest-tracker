<?php

return [
    'title' => 'Notificaciones',
    'label' => 'Notificaciones',

    // Database notifications
    'database' => 'Base de datos',
    'database_description' => 'Las notificaciones enviadas a este canal se mostrarán bajo el icono 🔔 en el encabezado.',
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
    'apprise_channels' => 'Canales de notificación',
    'apprise_channel_url' => 'URL del canal',
    'apprise_hint_description' => 'Apprise le permite enviar notificaciones a más de 90 servicios. Debe alojar un servidor Apprise y configurar las URL del servicio a continuación.',
    'apprise_channel_url_helper' => 'Utilice el formato URL de Apprise. Ejemplos: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Guarde sus configuraciones para probar las notificaciones.',
    'test_apprise_channel' => 'Prueba de Apprise',
    'apprise_channel_url_validation_error' => 'La URL de Apprise no es válida. Debe usar el formato Apprise (por ejemplo, discord://, slack://), no http:// o https://. Consulte la documentación de Apprise para obtener más información',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Probar canal webhook',
    'webhook_hint_description' => 'Estos son webhooks genéricos. Para ejemplos de carga útil y detalles de la implementación, vea la documentación. Para servicios como Discord, Ntfy, etc., utilice Apprise.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificar en cada prueba de velocidad programada',
    'notify_on_every_speedtest_run_helper' => 'Esto enviará una notificación por cada prueba de velocidad programada completada, solo para pruebas de salud o sin referencia',
    'notify_on_threshold_failures' => 'Notificar fallos de umbral para pruebas de velocidad programadas',
    'notify_on_threshold_failures_helper' => 'Esto enviará una notificación cuando una prueba de velocidad programada falle cualquiera de los umbrales configurados.',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Yo digo: ping',
            'pong' => 'Tú dices: pong',
            'received' => 'Notificación de la base de datos de prueba recibida!',
            'sent' => 'Notificación de prueba de base de datos enviada.',
        ],
        'mail' => [
            'add' => '¡Añade destinatarios al correo!',
            'sent' => 'Notificación de prueba de correo enviada.',
        ],
        'webhook' => [
            'add' => '¡Añade la URL del webhook!',
            'sent' => 'Prueba de notificación de webhook enviada.',
            'payload' => 'Probando notificación de webhook',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Las notificaciones del umbral se enviarán a la ruta /fail en la URL.',
];
