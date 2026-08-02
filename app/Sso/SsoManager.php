<?php

namespace App\Sso;

use App\Settings\SsoSettings;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class SsoManager
{
    public function __construct(
        private readonly Socialite $socialite,
        private readonly SsoSettings $settings,
    ) {}

    public function enabled(): bool
    {
        $override = config('sso.enabled');

        if ($override !== null) {
            return filter_var($override, FILTER_VALIDATE_BOOLEAN);
        }

        return (bool) $this->settings->enabled;
    }

    public function activeProvider(): ?string
    {
        return config('sso.provider') ?: ($this->settings->provider ?: null);
    }

    public function knows(string $provider): bool
    {
        return $provider === $this->activeProvider()
            && array_key_exists($provider, (array) config('sso.providers'));
    }

    /**
     * @return array<string, array{label: string, icon: ?string}>
     */
    public function enabledProviders(): array
    {
        if (! $this->enabled()) {
            return [];
        }

        $key = $this->activeProvider();

        if (! $key || ! config("sso.providers.{$key}")) {
            return [];
        }

        return [
            $key => [
                'label' => $this->buttonLabel($key),
                'icon' => config("sso.providers.{$key}.icon"),
            ],
        ];
    }

    public function driver(string $provider): AbstractProvider
    {
        $driver = $this->driverName($provider);

        config([
            "services.{$driver}" => [
                'client_id' => $this->clientId(),
                'client_secret' => $this->clientSecret(),
                'base_url' => $this->baseUrl(),
                'redirect' => route('sso.callback', $provider),
            ],
        ]);

        /** @var AbstractProvider $socialite */
        $socialite = $this->socialite->driver($driver);

        return $socialite->setScopes($this->scopes($provider));
    }

    public function buttonLabel(string $provider): string
    {
        if (filled($this->settings->button_label)) {
            return $this->settings->button_label;
        }

        $label = config("sso.providers.{$provider}.label", $provider);

        return __('settings/sso.sign_in_with', ['provider' => $label]);
    }

    public function clientId(): ?string
    {
        return config('sso.override.client_id') ?: $this->settings->client_id;
    }

    public function clientSecret(): ?string
    {
        return config('sso.override.client_secret') ?: $this->settings->client_secret;
    }

    public function baseUrl(): ?string
    {
        return config('sso.override.base_url') ?: $this->settings->base_url;
    }

    /**
     * @return array<int, string>
     */
    public function scopes(string $provider): array
    {
        $override = config('sso.override.scopes');

        if (filled($override)) {
            return array_values(array_filter(array_map('trim', explode(',', $override))));
        }

        if (filled($this->settings->scopes)) {
            return $this->settings->scopes;
        }

        return config("sso.providers.{$provider}.scopes", ['openid', 'profile', 'email']);
    }

    private function driverName(string $provider): string
    {
        return config("sso.providers.{$provider}.driver", $provider);
    }
}
