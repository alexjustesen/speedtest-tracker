<?php

return [
    'title' => 'Notifikace',
    'label' => 'Notifikace',

    // Database notifications
    'database' => 'Databáze',
    'database_description' => 'Notifikace odeslané do tohoto kanálu se zobrazí pod ikonou 🔔 v záhlaví.',
    'test_database_channel' => 'Otestovat databázový kanál',

    // Mail notifications
    'mail' => 'E-mail',
    'recipients' => 'Příjemci',
    'test_mail_channel' => 'Otestovat e-mailový kanál',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Povolit notifikace Apprise',
    'apprise_server' => 'Apprise server',
    'apprise_server_url' => 'URL Apprise serveru',
    'apprise_server_url_helper' => 'URL vašeho Apprise serveru. URL musí končit /notify',
    'apprise_verify_ssl' => 'Ověřovat SSL',
    'apprise_channels' => 'Kanály notifikací',
    'apprise_channel_url' => 'URL služby',
    'apprise_hint_description' => 'Apprise umožňuje odesílat notifikace do více než 90 služeb. Musíte mít spuštěný Apprise server a nakonfigurovat níže URL služeb.',
    'apprise_channel_url_helper' => 'Použijte formát URL Apprise. Příklady: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Uložte nastavení pro test notifikace.',
    'test_apprise_channel' => 'Otestovat Apprise',
    'apprise_channel_url_validation_error' => 'Neplatná URL Apprise. Musí být ve formátu Apprise (např. discord://, slack://), nelze použít http:// nebo https://. Pro více informací viz dokumentace Apprise.',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooks',
    'test_webhook_channel' => 'Otestovat webhook kanál',
    'webhook_hint_description' => 'Toto jsou generické webhooky. Pro příklady payloadu a implementační detaily viz dokumentace. Pro služby jako Discord, Ntfy apod. použijte Apprise.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Odeslat notifikaci při každém dokončeném plánovaném testu rychlosti',
    'notify_on_every_speedtest_run_helper' => 'Tímto se pošle notifikace pro každý dokončený plánovaný test rychlosti, pouze pro zdravé nebo netestované testy',
    'notify_on_threshold_failures' => 'Odeslat notifikaci při překročení prahových hodnot plánovaných testů rychlosti',
    'notify_on_threshold_failures_helper' => 'Tímto se pošle notifikace, pokud plánovaný test rychlosti nesplní některý z nastavených prahů',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Já říkám: ping',
            'pong' => 'Ty říkáš: pong',
            'received' => 'Testovací databázová notifikace přijata!',
            'sent' => 'Testovací databázová notifikace odeslána.',
        ],
        'mail' => [
            'add' => 'Přidejte příjemce e-mailu!',
            'sent' => 'Testovací e-mailová notifikace odeslána.',
        ],
        'webhook' => [
            'add' => 'Přidejte URL webhooku!',
            'sent' => 'Testovací webhook notifikace odeslána.',
            'failed' => 'Webhook notifikace selhala.',
            'payload' => 'Testování webhook notifikace',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Notifikace prahových hodnot budou odesílány na /fail route v URL.',
];