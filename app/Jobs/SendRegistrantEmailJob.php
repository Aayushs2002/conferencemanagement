<?php

namespace App\Jobs;

use App\Mail\Conference\CustomRegistrantMail;
use App\Models\Conference\ConferenceRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegistrantEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $registrantId,
        public string $subject,
        public string $messageContent,
        public array $data,
        public string $conferenceName
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $registrant = ConferenceRegistration::with('user.userDetail.namePrefix')->find($this->registrantId);

        if (!$registrant || !$registrant->user || !$registrant->user->email) {
            return;
        }

        Mail::to($registrant->user->email)->send(
            new CustomRegistrantMail(
                $this->data,
                $this->subject,
                $this->messageContent,
                $this->conferenceName
            )
        );
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to send email to registrant ID: ' . $this->registrantId, [
            'error' => $exception->getMessage(),
            'subject' => $this->subject,
        ]);
    }
}
