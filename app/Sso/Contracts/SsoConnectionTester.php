<?php

namespace App\Sso\Contracts;

use App\Sso\SsoConnectionResult;

interface SsoConnectionTester
{
    public function test(string $provider, ?string $baseUrl, ?string $clientId, ?string $clientSecret): SsoConnectionResult;
}
