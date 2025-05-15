<?php

namespace App\Listeners;

use App\Events\RequirementApproved;
use App\Models\User;
use App\Notifications\RequirementAvailableNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVendorNotifications implements ShouldQueue
{
    public function handle(RequirementApproved $event)
    {
        $requirement = $event->requirement;

        // Only send notifications if:
        // 1. Custom percentage was not used, OR
        // 2. Custom percentage was used AND requirement is approved
        if (!$requirement->needs_hod_approval || ($requirement->needs_hod_approval && $requirement->is_approved)) {
            // Get all vendors
            $vendors = User::role('vendor')->get();

            // Notify each vendor
            foreach ($vendors as $vendor) {
                $vendor->notify(new RequirementAvailableNotification($requirement));
            }
        }
    }
} 