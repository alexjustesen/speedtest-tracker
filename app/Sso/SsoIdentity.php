<?php

namespace App\Sso;

use Laravel\Socialite\Contracts\User as SocialiteUser;

final readonly class SsoIdentity
{
    /**
     * @param  array<int, string>  $groups
     */
    public function __construct(
        public string $provider,
        public string $id,
        public ?string $email,
        public bool $emailVerified,
        public ?string $name,
        public array $groups,
    ) {}

    public static function fromSocialite(string $provider, SocialiteUser $user, string $groupsClaim): self
    {
        $raw = $user->getRaw();

        return new self(
            provider: $provider,
            id: (string) $user->getId(),
            email: $user->getEmail(),
            emailVerified: (bool) ($raw['email_verified'] ?? false),
            name: $user->getName(),
            groups: array_values(array_filter((array) data_get($raw, $groupsClaim, []), 'is_string')),
        );
    }

    /**
     * @param  array<int, string>  $groups
     */
    public function isInAnyGroup(array $groups): bool
    {
        return (bool) array_intersect($this->groups, $groups);
    }
}
