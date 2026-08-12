<?php

namespace App\Settings;

use Spatie\LaravelSettings\Attributes\ShouldBeEncrypted;
use Spatie\LaravelSettings\Settings;

class SsoSettings extends Settings
{
    public bool $enabled;

    public string $provider;

    public ?string $client_id;

    #[ShouldBeEncrypted]
    public ?string $client_secret;

    public ?string $base_url;

    public array $scopes;

    public ?string $button_label;

    public bool $auto_create_users;

    public bool $allow_linking_by_email;

    public string $default_role;

    public bool $role_mapping_enabled;

    public string $groups_claim;

    public array $admin_groups;

    public static function group(): string
    {
        return 'sso';
    }
}
