<?php

namespace App\Sso\Contracts;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

interface SsoUserResolver
{
    public function resolve(string $provider, SocialiteUser $ssoUser): ?User;
}
