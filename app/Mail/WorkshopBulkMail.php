<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WorkshopBulkMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address'), 
                $this->mailData['conferenceName'] ?? config('mail.from.name')
            ),
            subject: $this->mailData['subject'] ?? 'Workshop Communication',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.workshop.bulk-mail',
            with: [
                'name' => $this->mailData['name'],
                'messageContent' => $this->mailData['messageContent'],
                'subject' => $this->mailData['subject'],
                'conferenceName' => $this->mailData['conferenceName'] ?? config('mail.from.name'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
