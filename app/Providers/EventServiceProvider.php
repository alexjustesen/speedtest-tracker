<?php

namespace App\Providers;

use App\Events\ResultCreated;
use App\Models\Result;
use App\Observers\ResultObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        ResultCreated::class => [
            \App\Listeners\SpeedtestCompletedListener::class,

            // Data listeners
            // TODO: add influxdb listener here

            // Threashold listeners
            \App\Listeners\Threshold\AbsoluteDownloadListener::class,
            \App\Listeners\Threshold\AbsoluteUploadListener::class,
            \App\Listeners\Threshold\AbsolutePingListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Result::observe(ResultObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
