<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;

class PrometheusServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind the Prometheus registry into the service container
        $this->app->singleton(CollectorRegistry::class, function () {
            return new CollectorRegistry(new InMemory);
        });
    }

    public function boot()
    {
        //
    }
}
