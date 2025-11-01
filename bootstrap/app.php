<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'getting-started' => App\Http\Middleware\GettingStarted::class,
            'public-dashboard' => App\Http\Middleware\PublicDashboard::class,
            'accept-json' => App\Http\Middleware\AcceptJsonMiddleware::class,
        ]);

        $middleware->prependToGroup('api', [
            App\Http\Middleware\AllowedIpAddressesMiddleware::class,
        ]);

        $middleware->prependToGroup('web', [
            App\Http\Middleware\AllowedIpAddressesMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('filament.admin.auth.login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->trustProxies(at: '*');

        $middleware->trustProxies(headers: Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_AWS_ELB
        );

        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
