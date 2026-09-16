<?php

namespace App\Providers;

use App\Sso\Contracts\SsoUserResolver;
use App\Sso\ResolveSsoUser;
use Illuminate\Support\ServiceProvider;

class SsoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SsoUserResolver::class, ResolveSsoUser::class);
    }
}
