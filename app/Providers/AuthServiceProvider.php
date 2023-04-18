<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

// TODO: remove package and policy as FailedJob is no longer included
use Amvisor\FilamentFailedJobs\Models\FailedJob;
use App\Models\Result;
use App\Policies\FailedJobPolicy;
use App\Policies\ResultPolicy;
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
        Result::class => ResultPolicy::class,
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
