<?php

namespace App\Sso;

use Laravel\Socialite\Contracts\User as SocialiteUser;

final readonly class SsoIdentity
{
    /**
     * @param  list<string>  $groups
     */
    public function __construct(
        public string $id,
        public ?string $email,
        public bool $emailVerified,
        public ?string $name,
        public array $groups,
    ) {}

    public static function fromSocialite(SocialiteUser $user, string $groupsClaim): self
    {
        $raw = $user->getRaw();

        $groups = data_get($raw, $groupsClaim, []);

        return new self(
            id: (string) $user->getId(),
            email: $user->getEmail(),
            emailVerified: filter_var($raw['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN),
            name: $user->getName(),
            groups: array_values(array_filter(is_array($groups) ? $groups : [], 'is_string')),
        );
    }

    /**
     * @param  list<string>  $groups
     */
    public function isInAnyGroup(array $groups): bool
    {
        return array_intersect($this->groups, $groups) !== [];
    }
}
