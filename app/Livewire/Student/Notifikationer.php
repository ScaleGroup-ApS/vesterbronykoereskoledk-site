<?php

namespace App\Livewire\Student;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;
use Livewire\Component;

class Notifikationer extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn (DatabaseNotification $n) => [
                'id' => $n->id,
                'type_label' => $this->typeLabel($n->type),
                'data' => $n->data,
                'read_at' => $n->read_at?->timezone(config('app.timezone'))->translatedFormat('d. M Y \k\l. H:i'),
                'created_at' => $n->created_at->timezone(config('app.timezone'))->translatedFormat('d. M Y \k\l. H:i'),
                'is_read' => $n->read_at !== null,
            ])
            ->values()
            ->all();

        return view('livewire.student.notifikationer', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(string $id): void
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    private function typeLabel(string $type): string
    {
        $shortName = class_basename($type);

        return match ($shortName) {
            'BookingReminderNotification' => 'Påmindelse om booking',
            'BookingScheduledNotification' => 'Ny booking',
            'BookingRescheduledNotification' => 'Booking ændret',
            'BookingCancelledNotification' => 'Booking aflyst',
            'EnrollmentApprovedNotification' => 'Tilmelding godkendt',
            default => $shortName,
        };
    }
}
