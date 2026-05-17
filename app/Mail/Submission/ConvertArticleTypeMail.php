<?php

namespace App\Mail\Submission;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConvertArticleTypeMail extends Mailable
{
    use Queueable, SerializesModels;

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

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), $this->conferenceName),
            subject: $this->subjectText ?: 'Recommendation to Change Presentation Category',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.submission.convert-article-type',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
