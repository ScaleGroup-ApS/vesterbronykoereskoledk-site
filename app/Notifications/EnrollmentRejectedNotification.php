<?php

namespace App\Notifications;

use App\Models\EnrollmentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Din tilmelding er afvist')
            ->greeting("Hej {$notifiable->name}!")
            ->line("Din tilmelding til {$this->enrollmentRequest->offer->name} er desværre blevet afvist.")
            ->when(
                $this->enrollmentRequest->rejection_reason,
                fn (MailMessage $mail) => $mail->line('Årsag: '.$this->enrollmentRequest->rejection_reason),
            )
            ->line('Kontakt os venligst for mere information.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'enrollment_request_id' => $this->enrollmentRequest->id,
            'offer_name' => $this->enrollmentRequest->offer->name,
            'rejection_reason' => $this->enrollmentRequest->rejection_reason,
        ];
    }
}
