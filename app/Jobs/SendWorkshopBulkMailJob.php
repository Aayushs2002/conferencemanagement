<?php

namespace App\Jobs;

use App\Mail\WorkshopBulkMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWorkshopBulkMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $user;
    protected $subject;
    protected $mailContent;
    protected $data;
    protected $conferenceName;
    
    public function __construct($user, $subject, $mailContent, $data = [], $conferenceName = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->mailContent = $mailContent;
        $this->data = $data;
        $this->conferenceName = $conferenceName ?? config('mail.from.name');
    }

    public function handle()
    {
        $mailData = [
            'name' => $this->data['name'] ?? $this->user->name ?? 'Participant',
            'email' => $this->user->email,
            'messageContent' => $this->mailContent,
            'subject' => $this->subject,
            'namePrefix' => $this->data['namePrefix'] ?? '',
            'registrant_type' => $this->data['registrant_type'] ?? 1,
            'workshop_title' => $this->data['workshop_title'] ?? '',
            'conferenceName' => $this->conferenceName,
        ];

        Mail::to($this->user->email)->send(new WorkshopBulkMail($mailData));
    }
}
