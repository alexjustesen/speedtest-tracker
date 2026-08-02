<?php

namespace App\Sso;

use App\Enums\UserRole;
use App\Models\User;
use App\Settings\SsoSettings;
use App\Sso\Contracts\SsoUserResolver;
use Laravel\Socialite\Contracts\User as SocialiteUser;

final class ResolveSsoUser implements SsoUserResolver
{
    public function __construct(
        private readonly SsoSettings $settings,
    ) {}

    public function resolve(string $provider, SocialiteUser $ssoUser): ?User
    {
        $identity = SsoIdentity::fromSocialite($provider, $ssoUser, $this->settings->groups_claim);

        $user = User::query()
            ->where('sso_provider', $identity->provider)
            ->where('sso_id', $identity->id)
            ->first();

        if ($user !== null) {
            return $this->sync($user, $identity);
        }

        $localUser = filled($identity->email)
            ? User::query()->where('email', $identity->email)->first()
            : null;

        if ($localUser !== null) {
            if ($this->settings->allow_linking_by_email && $identity->emailVerified) {
                $localUser->sso_provider = $identity->provider;
                $localUser->sso_id = $identity->id;

                return $this->sync($localUser, $identity);
            }

            return null;
        }

        if ($this->settings->auto_create_users && filled($identity->email)) {
            $user = new User([
                'name' => $identity->name ?: $identity->email,
                'email' => $identity->email,
                'sso_provider' => $identity->provider,
                'sso_id' => $identity->id,
            ]);

            $user->password = bin2hex(random_bytes(32));
            $user->email_verified_at = $identity->emailVerified ? now() : null;
            $user->role = $this->resolveRole($identity);
            $user->save();

            return $user;
        }

        return null;
    }

    private function sync(User $user, SsoIdentity $identity): User
    {
        if (filled($identity->name)) {
            $user->name = $identity->name;
        }

        if (filled($identity->email)) {
            $user->email = $identity->email;
        }

        if ($this->settings->role_mapping_enabled) {
            $user->role = $this->resolveRole($identity);
        }

        $user->save();

        return $user;
    }

    private function resolveRole(SsoIdentity $identity): UserRole
    {
        if (! $this->settings->role_mapping_enabled) {
            return UserRole::tryFrom($this->settings->default_role) ?? UserRole::User;
        }

        return $identity->isInAnyGroup($this->settings->admin_groups)
            ? UserRole::Admin
            : UserRole::User;
    }
}
