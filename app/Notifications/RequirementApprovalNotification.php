<?php

namespace App\Notifications;

use App\Models\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequirementApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $requirement;

    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = route('requirements.show', $this->requirement);

        return (new MailMessage)
            ->subject('Custom Percentage Approval Required')
            ->line('A new requirement needs your approval for custom percentage.')
            ->line('Requirement ID: ' . $this->requirement->requirement_id)
            ->line('Custom Percentage: ' . $this->requirement->custom_percentage . '%')
            ->line('Created By: ' . $this->requirement->createBy->name)
            ->action('Review Requirement', $url)
            ->line('Please review and approve the custom percentage for this requirement.');
    }
} 