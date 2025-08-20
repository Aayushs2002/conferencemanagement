<?php

namespace App\Mail\Submission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionSubmittedToUserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data; 
    public $subjectText;
    public $bodyContent;

    public function __construct($data, $subjectText, $bodyContent)
    {
        $this->data = $data;
        $this->subjectText = $subjectText;
        $this->bodyContent = $bodyContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // dd($this->bodyContent, 2);
        return new Envelope(
            subject: $this->subjectText ? $this->subjectText : 'Thank You for Your Abstract Submission',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission.submitted-to-user',
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
