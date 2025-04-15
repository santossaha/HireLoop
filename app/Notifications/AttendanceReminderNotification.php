<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pendingVendors;

    /**
     * Create a new notification instance.
     *
     * @param  array  $pendingVendors  List of vendor names pending attendance submission
     * @return void
     */
    public function __construct(array $pendingVendors)
    {
        $this->pendingVendors = $pendingVendors;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('Reminder: Vendor Attendance Submission Required')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that you need to submit attendance records for the following vendors:');
            
        // Add each vendor as a separate line
        foreach ($this->pendingVendors as $vendorName) {
            $mailMessage->line('- ' . $vendorName);
        }
        
        $mailMessage->line('Please submit the attendance records by the 25th of this month.')
            ->action('Submit Attendance', route('attendance.index'))
            ->line('Thank you for your prompt attention to this matter.');
            
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Attendance Submission Reminder',
            'message' => 'You have ' . count($this->pendingVendors) . ' vendors pending attendance submission for this month.',
            'pending_vendors' => $this->pendingVendors,
            'action_url' => route('attendance.index'),
        ];
    }
}
