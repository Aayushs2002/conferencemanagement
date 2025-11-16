<?php

namespace App\Mail\Submission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionRejectMail extends Mailable
{
    use Queueable, SerializesModels;

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
            subject: $this->subjectText ? $this->subjectText : 'Submission Reject Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission.rejected',
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
