<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSsoSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('sso.enabled', false);
        $this->migrator->add('sso.provider', 'authentik');
        $this->migrator->add('sso.client_id', null);
        $this->migrator->addEncrypted('sso.client_secret', null);
        $this->migrator->add('sso.base_url', null);
        $this->migrator->add('sso.scopes', ['openid', 'profile', 'email']);
        $this->migrator->add('sso.button_label', null);
        $this->migrator->add('sso.auto_create_users', true);
        $this->migrator->add('sso.allow_linking_by_email', true);
        $this->migrator->add('sso.default_role', 'user');
        $this->migrator->add('sso.role_mapping_enabled', false);
        $this->migrator->add('sso.groups_claim', 'groups');
        $this->migrator->add('sso.admin_groups', []);
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('sso.enabled');
        $this->migrator->deleteIfExists('sso.provider');
        $this->migrator->deleteIfExists('sso.client_id');
        $this->migrator->deleteIfExists('sso.client_secret');
        $this->migrator->deleteIfExists('sso.base_url');
        $this->migrator->deleteIfExists('sso.scopes');
        $this->migrator->deleteIfExists('sso.button_label');
        $this->migrator->deleteIfExists('sso.auto_create_users');
        $this->migrator->deleteIfExists('sso.allow_linking_by_email');
        $this->migrator->deleteIfExists('sso.default_role');
        $this->migrator->deleteIfExists('sso.role_mapping_enabled');
        $this->migrator->deleteIfExists('sso.groups_claim');
        $this->migrator->deleteIfExists('sso.admin_groups');
    }
}
