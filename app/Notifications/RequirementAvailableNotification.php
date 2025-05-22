<?php

namespace App\Notifications;

use App\Models\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequirementAvailableNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requirement;

    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $url = route('requirements.show', $this->requirement);

        return (new MailMessage)
            ->subject('New Requirement Available')
            ->line('A new requirement is available for sourcing.')
            ->line('Requirement ID: ' . $this->requirement->requirement_id)
            ->line('Company: ' . $this->requirement->company->name)
            ->line('Department: ' . $this->requirement->department->name)
            ->action('View Requirement', $url)
            ->line('Please check the requirement details and start sourcing candidates if interested.');
    }

    public function toArray($notifiable)
    {
        return [
            'requirement_id' => $this->requirement->id,
            'requirement_code' => $this->requirement->requirement_id,
            'message' => 'New requirement available for sourcing',
            'url' => route('requirements.show', $this->requirement)
        ];
    }
} 