<?php

return [
    'title' => 'Notificações',
    'label' => 'Notificações',

    // Database notifications
    'database' => 'Banco de Dados',
    'database_description' => 'Notificações enviadas para este canal aparecerão sob o 🔔 ícone no cabeçalho.',
    'test_database_channel' => 'Testar canal do banco de dados',

    // Mail notifications
    'mail' => 'E-mail',
    'recipients' => 'Destinatários',
    'test_mail_channel' => 'Testar canal de e-mail',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Habilitar notificações Apprise',
    'apprise_server' => 'Servidor Apprise',
    'apprise_server_url' => 'URL do Servidor Apprise',
    'apprise_server_url_helper' => 'A URL do seu Servidor Apprise. A URL deve terminar em /notify',
    'apprise_verify_ssl' => 'Verificar SSL',
    'apprise_channels' => 'Canais de notificação',
    'apprise_channel_url' => 'URL de serviço',
    'apprise_hint_description' => 'Apprise permite que você envie notificações para mais de 90 serviços. Você precisa executar um servidor Apprise e configurar as URLs de serviço abaixo.',
    'apprise_channel_url_helper' => 'Use o formato de URL Apprise. Exemplos: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Salve suas configurações para testar a notificação.',
    'test_apprise_channel' => 'Testar Apprise',
    'apprise_channel_url_validation_error' => 'URL Apprise inválida. Deve usar o formato Apprise (por exemplo, discord://, slack://), não http:// ou https://. Consulte a documentação Apprise para obter mais informações',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Testar canal webhook',
    'webhook_hint_description' => 'Estes são webhooks genéricos. Para exemplos de payload e detalhes de implementação, consulte a documentação. Para serviços como Discord, Ntfy e etc. por favor use Apprise.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Notifique após cada teste de velocidade agendado ser concluído',
    'notify_on_every_speedtest_run_helper' => 'Isso enviará uma notificação para cada execução de teste de velocidade agendada concluída, apenas para testes sem erros ou não avaliados',
    'notify_on_threshold_failures' => 'Notificar sobre falhas nos limites de testes de velocidade agendados',
    'notify_on_threshold_failures_helper' => 'Isso enviará uma notificação quando um teste de velocidade agendado não atingir nenhum dos limites configurados',

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
            'failed' => 'A notificação do webhook falhou.',
            'payload' => 'Testando notificação webhook',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Notificações de limite serão enviadas para a rota /fail na URL.',
];
