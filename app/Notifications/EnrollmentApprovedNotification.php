<?php

namespace App\Notifications;

use App\Models\EnrollmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly EnrollmentRequest $enrollmentRequest) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Din tilmelding er godkendt')
            ->greeting("Hej {$notifiable->name}!")
            ->line("Din tilmelding til {$this->enrollmentRequest->offer->name} er blevet godkendt.")
            ->line('Du er nu officielt tilmeldt og kan tilgå dit dashboard.')
            ->action('Gå til dashboard', route('dashboard'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'enrollment_request_id' => $this->enrollmentRequest->id,
            'offer_name' => $this->enrollmentRequest->offer->name,
        ];
    }
}
