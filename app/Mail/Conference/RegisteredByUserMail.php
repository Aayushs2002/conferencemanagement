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

class RegisteredByUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /** 
     * Create a new message instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Conference Registration Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.conference.registration-by-user',
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
        // $pdf = Pdf::loadView('emails.conference.payment-voucher', ['data' => $this->data])
        //     ->setPaper('legal', 'potrait');
        // $pdfPath = storage_path('app/public/registration.pdf');
        // $pdf->save($pdfPath);
        // return [
        //     Attachment::fromPath($pdfPath)
        //         ->as('PaymentVoucher.pdf')
        //         ->withMime('application/pdf'),
        // ];
    }
}
