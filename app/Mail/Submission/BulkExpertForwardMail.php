<?php

namespace App\Mail\Submission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BulkExpertForwardMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public $subjectText;
    public $bodyContent;
    public $conferenceName;

    public function __construct($data, $subjectText, $bodyContent, $conferenceName = null)
    {
        $this->data = $data;
        $this->subjectText = $subjectText;
        $this->bodyContent = $bodyContent;
        $this->conferenceName = $conferenceName ?? config('mail.from.name');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), $this->conferenceName),
            subject: $this->subjectText ? $this->subjectText : 'Multiple Submissions Assigned For Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.submission.bulk-expert-assigned',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
