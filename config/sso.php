<?php

return [

    'enabled' => env('SSO_ENABLED'),

    'provider' => env('SSO_PROVIDER'),

    'override' => [
        'client_id' => env('SSO_CLIENT_ID'),
        'client_secret' => env('SSO_CLIENT_SECRET'),
        'base_url' => env('SSO_BASE_URL'),
        'scopes' => env('SSO_SCOPES'),
    ],

    'providers' => [

        'authentik' => [
            'driver' => 'authentik',
            'label' => 'Authentik',
            'icon' => 'tabler-shield-lock',
            'scopes' => ['openid', 'profile', 'email'],
            'token_path' => '/application/o/token/',
        ],

    ],

];
