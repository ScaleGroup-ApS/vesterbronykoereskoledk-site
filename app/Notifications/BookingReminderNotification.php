<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReminderNotification extends Notification implements ShouldQueue
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

        $isStudent = $notifiable instanceof User && $notifiable->isStudent();

        $actionText = $isStudent ? 'Se kalenderen' : 'Se bookinger';
        $actionUrl = $isStudent
            ? route('student.calendar')
            : route('bookings.index');

        return (new MailMessage)
            ->subject('Påmindelse om booking i morgen')
            ->greeting("Hej {$notifiable->name}!")
            ->line("Du har en booking i morgen: {$label}")
            ->line("Tidspunkt: {$this->booking->starts_at->format('H:i')} — {$this->booking->ends_at->format('H:i')}")
            ->action($actionText, $actionUrl)
            ->line('Husk at møde til tiden.');
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
