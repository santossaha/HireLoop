<?php

namespace App\Events;

use App\Models\Requirement;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequirementCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $requirement;

    /**
     * Create a new event instance.
     */
    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('requirements');
    }

    public function broadcastWith()
    {
        return [
            'requirement' => [
                'id' => $this->requirement->id,
                'requirement_id' => $this->requirement->requirement_id,
                'company' => $this->requirement->company->name,
                'department' => $this->requirement->department->name,
                'created_at' => $this->requirement->created_at
            ]
        ];
    }
} 