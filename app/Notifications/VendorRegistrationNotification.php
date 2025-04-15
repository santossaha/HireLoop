<?php

namespace App\Notifications;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $vendor;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Vendor  $vendor
     * @return void
     */
    public function __construct(Vendor $vendor)
    {
        $this->vendor = $vendor;
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
        $url = route('vendors.show', $this->vendor->id);

        return (new MailMessage)
            ->subject('New Vendor Registration: ' . $this->vendor->company_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new vendor has been registered and assigned to you as the internal POC.')
            ->line('Vendor Name: ' . $this->vendor->company_name)
            ->line('Contact Person: ' . $this->vendor->contact_person)
            ->line('Vendor Type: ' . ucfirst($this->vendor->vendor_type))
            ->action('View Vendor Details', $url)
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
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->company_name,
            'message' => 'New vendor registration assigned to you: ' . $this->vendor->company_name,
            'action_url' => route('vendors.show', $this->vendor->id),
        ];
    }
}
