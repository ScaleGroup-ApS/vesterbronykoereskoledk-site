<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingScheduledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Booking $booking) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $label = $this->booking->type->label();
        $when = $this->booking->starts_at->timezone(config('app.timezone'))->translatedFormat('l j. F Y \k\l. H:i');

        return (new MailMessage)
            ->subject('Ny booking hos '.config('app.name'))
            ->greeting("Hej {$notifiable->name}!")
            ->line("Der er booket en ny aktivitet: {$label}.")
            ->line("Tidspunkt: {$when}")
            ->action('Se kalenderen', route('student.kalender'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => $this->booking->type->value,
            'starts_at' => $this->booking->starts_at->toISOString(),
        ];
    }
}
