<?php

namespace App\Sso;

use App\Enums\UserRole;
use Illuminate\Contracts\Config\Repository as Config;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Two\AbstractProvider;

final class SsoManager
{
    public const DRIVER = 'openidconnect';

    private const CONFIG = 'services.'.self::DRIVER;

    public function __construct(
        private readonly Socialite $socialite,
        private readonly Config $config,
    ) {}

    public function enabled(): bool
    {
        return $this->flag('enabled')
            && $this->text('base_url') !== null
            && $this->text('client_id') !== null
            && $this->text('client_secret') !== null;
    }

    public function driver(): AbstractProvider
    {
        $this->config->set(self::CONFIG.'.redirect', $this->redirectUri());

        /** @var AbstractProvider $provider */
        $provider = $this->socialite->driver(self::DRIVER);

        return $provider;
    }

    public function redirectUri(): string
    {
        return $this->text('redirect') ?? route('sso.callback');
    }

    public function buttonLabel(): string
    {
        return $this->text('button_label') ?? __('auth.sso.sign_in');
    }

    public function autoProvision(): bool
    {
        return $this->flag('auto_provision');
    }

    public function groupsClaim(): string
    {
        return $this->text('groups_claim') ?? 'groups';
    }

    public function mapsGroupsToRoles(): bool
    {
        return $this->adminGroups() !== [];
    }

    /**
     * @return list<string>
     */
    public function adminGroups(): array
    {
        $groups = $this->config->get(self::CONFIG.'.admin_groups');

        if (is_string($groups)) {
            $groups = explode(',', $groups);
        }

        if (! is_array($groups)) {
            return [];
        }

        $names = array_map(
            static fn (mixed $group): string => is_string($group) ? trim($group) : '',
            $groups,
        );

        return array_values(array_filter($names, static fn (string $name): bool => $name !== ''));
    }

    public function defaultRole(): UserRole
    {
        return UserRole::tryFrom($this->text('default_role') ?? '') ?? UserRole::User;
    }

    private function text(string $key): ?string
    {
        $value = $this->config->get(self::CONFIG.'.'.$key);

        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        return trim($value);
    }

    private function flag(string $key): bool
    {
        return filter_var($this->config->get(self::CONFIG.'.'.$key), FILTER_VALIDATE_BOOLEAN);
    }
}
