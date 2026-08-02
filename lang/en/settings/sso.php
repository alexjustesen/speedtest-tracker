<?php

return [
    'title' => 'Single Sign-On',
    'label' => 'Single Sign-On',

    // Master switch
    'enabled' => 'Enable Single Sign-On',
    'enabled_helper' => 'When enabled, a sign-in button for the configured provider is shown on the login screen. Password login stays available.',

    // Connection
    'connection' => 'Connection',
    'provider' => 'Provider',
    'base_url' => 'Base URL',
    'base_url_helper' => 'The root URL of your identity provider, e.g. https://authentik.example.com',
    'client_id' => 'Client ID',
    'client_secret' => 'Client Secret',
    'scopes' => 'Scopes',
    'scopes_helper' => 'OAuth scopes requested at login. For Authentik, the "profile" scope also returns group membership.',
    'button_label' => 'Button label',
    'button_label_helper' => 'Optional custom text for the login button. Defaults to "Sign in with <provider>".',
    'redirect_uri' => 'Redirect URI',
    'redirect_uri_helper' => 'Register this exact URL as the redirect/callback URI in your provider.',
    'test_connection' => 'Test connection',

    // Provisioning
    'provisioning' => 'User provisioning',
    'auto_create_users' => 'Auto-create users',
    'auto_create_users_helper' => 'Create a local account the first time an unknown user signs in through SSO.',
    'allow_linking_by_email' => 'Link by email',
    'allow_linking_by_email_helper' => 'Link an SSO identity to an existing local account when the provider reports a verified, matching email address.',
    'default_role' => 'Default role',
    'default_role_helper' => 'Role assigned to newly created SSO users (unless overridden by group mapping).',

    // Role mapping
    'role_mapping' => 'Role mapping',
    'role_mapping_enabled' => 'Map provider groups to roles',
    'role_mapping_enabled_helper' => 'Derive the application role from the provider group membership on every sign-in.',
    'groups_claim' => 'Groups claim',
    'groups_claim_helper' => 'The claim that contains the user\'s groups (Authentik returns "groups").',
    'admin_groups' => 'Admin groups',
    'admin_groups_helper' => 'Users in any of these groups become administrators; everyone else becomes a regular user.',

    // Login screen
    'or' => 'or',
    'sign_in_with' => 'Sign in with :provider',

    // Flash messages
    'callback_failed' => 'Single Sign-On failed. Please try again or sign in with your password.',
    'not_provisioned' => 'Your account is not provisioned for access. Contact an administrator.',

    // Test connection notifications
    'test_missing_config' => 'Configure and save the provider details before testing.',
    'test_unreachable' => 'Could not reach the provider. Check the base URL and network connectivity.',
    'test_success' => 'Connection successful — the provider is reachable and the client is recognised.',
    'test_failed' => 'Connection failed — check the client ID and secret.',
];
