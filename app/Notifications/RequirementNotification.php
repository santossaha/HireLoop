<?php

namespace App\Notifications;

use App\Constants\NotificationMessages;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class RequirementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requirement;
    protected $type;
    protected $additionalData;

    public function __construct($requirement, $type = 'created', $additionalData = [])
    {
        $this->requirement = $requirement;
        $this->type = $type;
        $this->additionalData = $additionalData;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    protected function getTitle()
    {
        $replacements = [
            ':company' => $this->requirement->company->name,
            ':department' => $this->requirement->department->name,
            ':requirement_id' => $this->requirement->requirement_id,
            ':company_name' => $this->requirement->company->name
        ];

        if (isset($this->additionalData['candidate_name'])) {
            $replacements[':candidate_name'] = $this->additionalData['candidate_name'];
        }

        if (isset($this->additionalData['interview_datetime'])) {
            $replacements[':interview_datetime'] = $this->additionalData['interview_datetime'];
        }

        return NotificationMessages::format(
            NotificationMessages::NEW_REQUIREMENT_TITLE,
            $replacements
        );
    }

    protected function getMessage()
    {
        $replacements = [
            ':company_name' => $this->requirement->company->name,
            ':requirement_id' => $this->requirement->requirement_id
        ];

        // Choose message based on user role and type
        if ($this->notifiable->role === 'vendor') {
            return NotificationMessages::format(
                NotificationMessages::NEW_REQUIREMENT_MESSAGE_VENDOR,
                $replacements
            );
        } else {
            return NotificationMessages::format(
                NotificationMessages::NEW_REQUIREMENT_MESSAGE_ADMIN,
                $replacements
            );
        }
    }

    public function toArray($notifiable)
    {
        $this->notifiable = $notifiable;
        return [
            'requirement_id' => $this->requirement->id,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'type' => $this->type,
            'created_at' => now()
        ];
    }

    public function toBroadcast($notifiable)
    {
        $this->notifiable = $notifiable;
        return new BroadcastMessage([
            'requirement_id' => $this->requirement->id,
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'type' => $this->type,
            'created_at' => now()
        ]);
    }
} 