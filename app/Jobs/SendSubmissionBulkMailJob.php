<?php

namespace App\Jobs;

use App\Mail\SubmissionBulkMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendSubmissionBulkMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $user;
    protected $subject;
    protected $mailContent;
    public function __construct($user, $subject, $mailContent)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->mailContent = $mailContent;
    }

    public function handle()
    {
        $mailData = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'content' => $this->mailContent,
            'subject' => $this->subject,
        ];

        Mail::to($this->user->email)->send(new SubmissionBulkMail($mailData));
    }
}
