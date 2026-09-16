<?php

return [

    'openidconnect' => [
        'enabled' => (bool) env('OIDC_ENABLED', false),
        'base_url' => env('OIDC_BASE_URL'),
        'client_id' => env('OIDC_CLIENT_ID'),
        'client_secret' => env('OIDC_CLIENT_SECRET'),
        'redirect' => env('OIDC_REDIRECT_URI'),
        'scopes' => preg_split('/[\s,]+/', (string) env('OIDC_SCOPES', 'openid email profile'), -1, PREG_SPLIT_NO_EMPTY),
        'button_label' => env('OIDC_BUTTON_LABEL'),
        'auto_provision' => (bool) env('OIDC_AUTO_PROVISION', false),
        'groups_claim' => env('OIDC_GROUPS_CLAIM', 'groups'),
        'admin_groups' => env('OIDC_ADMIN_GROUPS'),
        'default_role' => env('OIDC_DEFAULT_ROLE', 'user'),
    ],

];
