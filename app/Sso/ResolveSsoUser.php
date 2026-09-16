<?php

namespace App\Sso;

use App\Enums\UserRole;
use App\Models\User;
use App\Sso\Contracts\SsoUserResolver;
use Laravel\Socialite\Contracts\User as SocialiteUser;

final class ResolveSsoUser implements SsoUserResolver
{
    public function __construct(
        private readonly SsoManager $manager,
    ) {}

    public function resolve(SocialiteUser $ssoUser): ?User
    {
        $identity = SsoIdentity::fromSocialite($ssoUser, $this->manager->groupsClaim());

        $user = User::query()
            ->where('sso_provider', SsoManager::DRIVER)
            ->where('sso_id', $identity->id)
            ->first();

        if ($user !== null) {
            return $this->sync($user, $identity);
        }

        $localUser = filled($identity->email)
            ? User::query()->where('email', $identity->email)->first()
            : null;

        if ($localUser !== null) {
            if (! $identity->emailVerified) {
                return null;
            }

            $localUser->sso_provider = SsoManager::DRIVER;
            $localUser->sso_id = $identity->id;

            return $this->sync($localUser, $identity);
        }

        if (! $this->manager->autoProvision() || blank($identity->email)) {
            return null;
        }

        $user = new User([
            'name' => $identity->name ?: $identity->email,
            'email' => $identity->email,
            'sso_provider' => SsoManager::DRIVER,
            'sso_id' => $identity->id,
        ]);

        $user->password = bin2hex(random_bytes(32));
        $user->email_verified_at = $identity->emailVerified ? now() : null;
        $user->role = $this->resolveRole($identity);
        $user->save();

        return $user;
    }

    private function sync(User $user, SsoIdentity $identity): User
    {
        if (filled($identity->name)) {
            $user->name = $identity->name;
        }

        if (filled($identity->email)) {
            $user->email = $identity->email;
        }

        if ($this->manager->mapsGroupsToRoles()) {
            $user->role = $this->resolveRole($identity);
        }

        $user->save();

        return $user;
    }

    private function resolveRole(SsoIdentity $identity): UserRole
    {
        if (! $this->manager->mapsGroupsToRoles()) {
            return $this->manager->defaultRole();
        }

        return $identity->isInAnyGroup($this->manager->adminGroups())
            ? UserRole::Admin
            : UserRole::User;
    }
}
