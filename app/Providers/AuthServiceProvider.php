<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\CandidateSourcing;
use App\Policies\CandidateSourcingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string|null>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        CandidateSourcing::class => CandidateSourcingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePolicies();

        //
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        //
    }

    /**
     * Configure the permissions for the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        //
    }

    /**
     * Configure the policies for the application.
     *
     * @return void
     */
    protected function configurePolicies()
    {
        //
    }
} 