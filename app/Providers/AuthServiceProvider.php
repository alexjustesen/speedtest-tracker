<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Amvisor\FilamentFailedJobs\Models\FailedJob;
use App\Policies\FailedJobPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        FailedJob::class => FailedJobPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
