<?php

namespace App\Jobs;

use App\Mail\SubmissionBulkMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSubmissionBulkMailJob implements ShouldQueue
{
    use Queueable;

    public $tries = 5;

    public $backoff = [30, 60, 120, 240];

    /**
     * Create a new job instance.
     */
    protected $user;
    protected $subject;
    protected $mailContent;
    protected $conferenceName;
    public function __construct($user, $subject, $mailContent, $conferenceName = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->mailContent = $mailContent;
        $this->conferenceName = $conferenceName ?? config('mail.from.name');
    }

    public function handle()
    {
        $mailData = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'content' => $this->mailContent,
            'subject' => $this->subject,
        ];

        Mail::to($this->user->email)->send(new SubmissionBulkMail($mailData, $this->conferenceName));
    }
}
