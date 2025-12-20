<?php

namespace App\Mail;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class MoreInfoRequestedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Complaint $complaint, 
        public string $messageContent
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Information Requested for Complaint: " . $this->complaint->reference_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.complaints.request_info', // Point to your actual blade file
            with: [
                'complaint' => $this->complaint,
                'messageContent' => $this->messageContent,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}