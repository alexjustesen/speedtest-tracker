<?php

namespace App\Providers;

use App\Sso\Contracts\SsoConnectionTester;
use App\Sso\Contracts\SsoUserResolver;
use App\Sso\ResolveSsoUser;
use App\Sso\TestSsoConnection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Authentik\Provider as AuthentikProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class SsoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SsoUserResolver::class, ResolveSsoUser::class);
        $this->app->bind(SsoConnectionTester::class, TestSsoConnection::class);
    }

    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('authentik', AuthentikProvider::class);
        });
    }
}
