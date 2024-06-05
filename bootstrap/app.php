<?php

use App\Providers\AppServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('admin/login'));
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
    })
    ->withSchedule(function (Schedule $schedule) {
        $settings = app(\App\Settings\GeneralSettings::class);

        /**
         * Checks if Result model records should be pruned.
         */
        if ($settings->prune_results_older_than > 0) {
            $schedule->command('model:prune', [
                '--model' => [\App\Models\Result::class],
            ])->daily();
        }

        /**
         * Checked for new versions weekly on Thursday because
         * I usually do releases on Thursday or Friday.
         */
        $schedule->command(\App\Console\Commands\VersionChecker::class)->weeklyOn(5)
            ->timezone($settings->timezone ?? 'UTC');

        /**
         * Action to run scheduled speedtests.
         */
        $schedule->call(function () {
            \App\Actions\Speedtests\RunScheduledSpeedtests::run();
        })
            ->name('Run scheduled speedtests')
            ->everyMinute()
            ->when(! blank($settings->speedtest_schedule));
    })->create();
