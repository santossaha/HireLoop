<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $type;
    protected $id;
    protected $title;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param  string  $type  Type of entity that needs approval (requirement, invoice, etc.)
     * @param  int  $id  ID of the entity
     * @param  string  $title  Title of the notification
     * @param  string  $message  Detailed message
     * @return void
     */
    public function __construct($type, $id, $title, $message)
    {
        $this->type = $type;
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
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
        $url = $this->getActionUrl();

        return (new MailMessage)
            ->subject($this->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->message)
            ->action('View Details', $url)
            ->line('Thank you for using our vendor management system!');
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
            'type' => $this->type,
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'action_url' => $this->getActionUrl(),
        ];
    }

    /**
     * Get the action URL based on the notification type
     *
     * @return string
     */
    protected function getActionUrl()
    {
        switch ($this->type) {
            case 'requirement':
                return route('requirements.show', $this->id);
            case 'invoice':
                return route('invoices.show', $this->id);
            case 'invoice_payment':
                return route('invoices.show', $this->id);
            case 'invoice_discrepancy':
                return route('invoices.show', $this->id);
            default:
                return route('dashboard');
        }
    }
}
