<?php

namespace App\Listeners;

use App\Events\RequirementCreated;
use App\Models\User;
use App\Notifications\NewRequirementNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendRequirementNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RequirementCreated $event): void
    {
        try {
            // Get all vendors
            $vendors = User::role('vendor')->get();
            
            // Send notification to each vendor
            foreach ($vendors as $vendor) {
                $vendor->notify(new NewRequirementNotification($event->requirement));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send requirement notifications: ' . $e->getMessage());
        }
    }
} 