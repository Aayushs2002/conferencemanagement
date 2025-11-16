<?php

namespace App\Mail\Conference;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Barryvdh\DomPDF\Facade\Pdf;

class ExceptionalRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public $conferenceName;

    /**
     * Create a new message instance.
     */
    public function __construct($data, $conferenceName = null)
    {
        $this->data = $data;
        $this->conferenceName = $conferenceName ?? config('mail.from.name');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(config('mail.from.address'), $this->conferenceName),
            subject: 'Comference Registration Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.conference.exceptional-case',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('emails.conference.payment-voucher', ['data' => $this->data])
            ->setPaper('legal', 'potrait');
        $pdfPath = storage_path('app/public/registration.pdf');
        $pdf->save($pdfPath);
        return [
            Attachment::fromPath($pdfPath)
                ->as('PaymentVoucher.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
