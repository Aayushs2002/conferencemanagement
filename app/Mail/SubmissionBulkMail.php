<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SubmissionBulkMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $mailData;
    public $conferenceName;

    /**
     * Create a new message instance.
     */
    public function __construct(array $mailData, $conferenceName = null)
    {
        $this->mailData = $mailData;
        $this->conferenceName = $conferenceName ?? config('mail.from.name');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), $this->conferenceName),
            subject: $this->mailData['subject'] ?? 'Submission Bulk Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission.bulk-mail',
            with: [
                'name' => $this->mailData['name'],
                'content' => $this->mailData['content'],
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
