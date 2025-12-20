<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ComplaintStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected Complaint $complaint;

    /**
     * Create a new notification instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Complaint Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("Your complaint #{$this->complaint->reference_number} has been updated.")
            ->line("New status: {$this->complaint->status}")
            ->line('Description: ' . $this->complaint->description)
            ->action('View Complaint', url("/complaints/reference/{$this->complaint->reference_number}"))
            ->line('Thank you for using our system.');
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
