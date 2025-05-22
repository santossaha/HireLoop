<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\Registered;
use App\Events\RequirementCreated;
use App\Listeners\SendEmailVerificationNotification;
use App\Listeners\SendRequirementNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        RequirementCreated::class => [
            SendRequirementNotification::class,
        ],
        'App\Events\RequirementApproved' => [
            'App\Listeners\SendVendorNotifications',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
} 