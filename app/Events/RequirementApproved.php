<?php

namespace App\Events;

use App\Models\Requirement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequirementApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $requirement;

    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }
} 