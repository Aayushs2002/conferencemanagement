<?php

namespace App\Mail\Submission;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConvertPresentationTypeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $data;
    public $conferenceName;
    public $subjectText;
    public $bodyContent;

    public function __construct($data, $conferenceName = null, $subjectText = null, $bodyContent = null)
    {
        $this->data = $data;
        $this->conferenceName = $conferenceName ?? config('mail.from.name');
        $this->subjectText = $subjectText;
        $this->bodyContent = $bodyContent;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), $this->conferenceName),
            subject: $this->subjectText ? $this->subjectText : 'Convert Presentation Type Request',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.submission.convert-presentation-type',
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
