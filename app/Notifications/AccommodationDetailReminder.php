<?php

namespace App\Notifications;

use App\Models\Conference\Conference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class AccommodationDetailReminder extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The conference instance.
     */
    public readonly Conference $conference;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
        $this->queue = 'notifications';
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $userName = $notifiable->f_name ?? 'Participant';
        $conferenceName = $this->conference->conference_name;
        
        return $this->buildMailMessage($userName, $conferenceName);
    }

    /**
     * Build the mail message.
     */
    private function buildMailMessage(string $userName, string $conferenceName): MailMessage
    {
        return (new MailMessage)
            ->subject("Update Your Accommodation Details - {$conferenceName}")
            ->view('emails.conference.accommodation-reminder', [
                'userName' => $userName,
                'conferenceName' => $conferenceName,
                'actionUrl' => $this->getAccommodationUrl(),
                'brand' => $this->conference->society->name ?: config('app.name'),
            ]);
    }

    /**
     * Get the accommodation URL with caching.
     */
    private function getAccommodationUrl(): string
    {
        $cacheKey = "accommodation_url_{$this->conference->society->id}_{$this->conference->id}";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () {
            return route('my-society.conference.accommodation', [
                'society' => $this->conference->society,
                'conference' => $this->conference
            ]);
        });
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'conference_id' => $this->conference->id,
            'conference_name' => $this->conference->conference_name,
            'type' => 'accommodation_reminder',
            'message' => 'Please update your accommodation details',
        ];
    }

    /**
     * Determine the notification's delivery delay.
     */
    // public function delay(object $notifiable): ?\DateTimeInterface
    // {
    //     // Add a small delay to prevent overwhelming the mail server
    //     return now()->addMinutes(rand(1, 5));
    // }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'accommodation_detail_reminder';
    }
}
