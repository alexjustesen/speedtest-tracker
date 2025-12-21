<?php

return [
    'title' => 'Notificações',
    'label' => 'Notificações',

    // Database notifications
    'database' => 'Banco de Dados',
    'database_description' => 'Notificações enviadas para este canal aparecerão sob o 🔔 ícone no cabeçalho.',
    'test_database_channel' => 'Testar canal do banco de dados',

    // Mail notifications
    'mail' => 'Correio',
    'recipients' => 'Destinatários',
    'test_mail_channel' => 'Testar canal de e-mail',

    // Apprise notifications
    'apprise' => 'Informar',
    'enable_apprise_notifications' => 'Habilitar notificações Apprise',
    'apprise_server' => 'Servidor Apprise',
    'apprise_server_url' => 'URL do Servidor Apprise',
    'apprise_verify_ssl' => 'Verificar SSL',
    'apprise_channels' => 'Canais Apprise',
    'apprise_channel_url' => 'URL do Canal',
    'apprise_hint_description' => 'Para obter mais informações sobre como configurar o Apprise, veja a documentação.',
    'apprise_channel_url_helper' => 'Forneça o URL do serviço endpoint para notificações.',
    'test_apprise_channel' => 'Testar Apprise',
    'apprise_channel_url_validation_error' => 'O URL do canal Apprise não deve começar com "http" ou "https". Por favor, forneça um esquema válido de URL Apprise.',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Testar canal webhook',
    'webhook_hint_description' => 'Estes são webhooks genéricos. Para exemplos de payload e detalhes de implementação, consulte a documentação.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notificar a cada execução do teste de velocidade',
    'notify_on_threshold_failures' => 'Notificar sobre falhas nos limites de testes de velocidade agendados',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Eu digo: ping',
            'pong' => 'Você diz: pong',
            'received' => 'Teste de notificação de banco de dados recebida!',
            'sent' => 'Teste de notificação do banco de dados enviada.',
        ],
        'mail' => [
            'add' => 'Adicione destinatários de email!',
            'sent' => 'Notificação de teste de email enviada.',
        ],
        'webhook' => [
            'add' => 'Adicionar URLs webhook!',
            'sent' => 'Notificação de teste webhook enviada.',
            'payload' => 'Testando notificação webhook',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Notificações de limite serão enviadas para a rota /fail na URL.',
];
