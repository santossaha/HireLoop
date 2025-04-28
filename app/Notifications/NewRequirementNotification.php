<?php

namespace App\Notifications;

use App\Models\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRequirementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $requirement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Requirement Created')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('A new requirement has been created that matches your department.')
            ->line('Job Description: ' . $this->requirement->job_description)
            ->line('Department: ' . $this->requirement->department->name)
            ->action('View Requirement', route('requirements.show', $this->requirement->id))
            ->line('Please review this requirement and upload suitable candidates if available.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'requirement_id' => $this->requirement->id,
            'job_description' => $this->requirement->job_description,
            'department' => $this->requirement->department->name
        ];
    }
}
