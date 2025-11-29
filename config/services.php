<?php

return [

    'apprise' => [
        'url' => env('APPRISE_URL', 'http://apprise:8000'),
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN'),
    ],

    'unifi-api' => [
        'base_url' => env('UNIFI_API_BASE_URL', 'https://192.168.1.1'),
        'token' => env('UNIFI_API_TOKEN'),
    ],

];
