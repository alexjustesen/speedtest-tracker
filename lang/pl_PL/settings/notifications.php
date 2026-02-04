<?php

return [
    'title' => 'Powiadomienia',
    'label' => 'Powiadomienia',

    // Database notifications
    'database' => 'Baza danych',
    'database_description' => 'Powiadomienia wysłane do tego kanału pojawią się pod ikoną 🔔 w nagłówku.',
    'test_database_channel' => 'Testuj kanał bazy danych',

    // Mail notifications
    'mail' => 'Email',
    'recipients' => 'Odbiorcy',
    'test_mail_channel' => 'Testuj kanał email',

    // Apprise notifications
    'apprise' => 'Apprise',
    'enable_apprise_notifications' => 'Włącz powiadomienia Apprise',
    'apprise_server' => 'Serwer Apprise',
    'apprise_server_url' => 'URL serwera Apprise',
    'apprise_server_url_helper' => 'URL Twojego serwera Apprise. URL musi kończyć się na /notify',
    'apprise_verify_ssl' => 'Weryfikuj SSL',
    'apprise_channels' => 'Kanały powiadomień',
    'apprise_channel_url' => 'URL usługi',
    'apprise_hint_description' => 'Apprise umożliwia wysyłanie powiadomień do ponad 90 usług. Musisz uruchomić serwer Apprise i skonfigurować URL-e usług poniżej.',
    'apprise_channel_url_helper' => 'Użyj formatu URL Apprise. Przykłady: discord://WebhookID/Token, slack://TokenA/TokenB/TokenC',
    'apprise_save_to_test' => 'Zapisz ustawienia, aby przetestować powiadomienie.',
    'test_apprise_channel' => 'Testuj Apprise',
    'apprise_channel_url_validation_error' => 'Nieprawidłowy URL Apprise. Musisz użyć formatu Apprise (np. discord://, slack://), nie http:// lub https://. Zobacz dokumentację Apprise, aby uzyskać więcej informacji',

    // Webhook
    'webhook' => 'Webhook',
    'webhooks' => 'Webhooki',
    'test_webhook_channel' => 'Testuj kanał webhook',
    'webhook_hint_description' => 'Są to ogólne webhooki. Przykłady ładunków i szczegóły implementacji znajdziesz w dokumentacji. Dla usług takich jak Discord, Ntfy itp. użyj Apprise.',

    // Common notification messages
    'notify_on_every_speedtest_run' => 'Powiadamiaj po każdym ukończonym zaplanowanym teście prędkości',
    'notify_on_every_speedtest_run_helper' => 'Wyśle powiadomienie po każdym ukończonym zaplanowanym teście prędkości, tylko dla zdrowych lub niebenchmarkowanych testów',
    'notify_on_threshold_failures' => 'Powiadamiaj o niepowodzeniach progów dla zaplanowanych testów prędkości',
    'notify_on_threshold_failures_helper' => 'Wyśle powiadomienie, gdy zaplanowany test prędkości nie powiedzie się na skonfigurowanych progach',

    // Test notification messages
    'test_notifications' => [
        'database' => [
            'ping' => 'Mówię: ping',
            'pong' => 'Ty mówisz: pong',
            'received' => 'Otrzymano powiadomienie testowe z bazy danych!',
            'sent' => 'Wysłano powiadomienie testowe z bazy danych.',
        ],
        'mail' => [
            'add' => 'Dodaj odbiorców email!',
            'sent' => 'Wysłano powiadomienie testowe email.',
        ],
        'webhook' => [
            'add' => 'Dodaj URL-e webhooków!',
            'sent' => 'Wysłano powiadomienie testowe webhook.',
            'failed' => 'Powiadomienie webhook nie powiodło się.',
            'payload' => 'Testowanie powiadomienia webhook',
        ],
    ],

    // Helper text
    'threshold_helper_text' => 'Powiadomienia o progach zostaną wysłane do trasy /fail w URL-u.',
];
