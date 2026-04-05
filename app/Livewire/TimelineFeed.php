<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class TimelineFeed extends Component
{
    public int $perPage = 25;

    public int $offset = 0;

    /** @var array<string, string> */
    private const array EVENT_CATEGORIES = [
        'App\Events\StudentEnrolled' => 'student',
        'App\Events\StudentStatusChanged' => 'student',
        'App\Events\StudentDeleted' => 'student',
        'App\Events\StudentLoginLinkSent' => 'student',
        'App\Events\BookingCreated' => 'booking',
        'App\Events\BookingUpdated' => 'booking',
        'App\Events\BookingCompleted' => 'booking',
        'App\Events\BookingCancelled' => 'booking',
        'App\Events\BookingNoShow' => 'booking',
        'App\Events\OfferAssigned' => 'payment',
        'App\Events\PaymentRecorded' => 'payment',
        'App\Events\EnrollmentRequested' => 'enrollment',
        'App\Events\EnrollmentApproved' => 'enrollment',
        'App\Events\EnrollmentRejected' => 'enrollment',
        'App\Events\StripePaymentCompleted' => 'enrollment',
        'App\Events\MaterialUnlockSet' => 'curriculum',
    ];

    /** @var array<string, string> */
    private const array EVENT_LABELS = [
        'StudentEnrolled' => 'Elev oprettet',
        'StudentStatusChanged' => 'Status ændret',
        'StudentDeleted' => 'Elev slettet',
        'StudentLoginLinkSent' => 'Login-link sendt',
        'BookingCreated' => 'Booking oprettet',
        'BookingUpdated' => 'Booking opdateret',
        'BookingCompleted' => 'Booking gennemført',
        'BookingCancelled' => 'Booking aflyst',
        'BookingNoShow' => 'Udeblivelse',
        'OfferAssigned' => 'Tilbud tildelt',
        'PaymentRecorded' => 'Betaling registreret',
        'EnrollmentRequested' => 'Tilmelding indsendt',
        'EnrollmentApproved' => 'Tilmelding godkendt',
        'EnrollmentRejected' => 'Tilmelding afvist',
        'StripePaymentCompleted' => 'Stripe-betaling gennemført',
        'MaterialUnlockSet' => 'Materiale låst op',
    ];

    public function loadMore(): void
    {
        $this->offset += $this->perPage;
    }

    public function getEventsProperty(): Collection
    {
        return DB::table('verb_events')
            ->orderByDesc('created_at')
            ->limit($this->offset + $this->perPage)
            ->get()
            ->map(function (object $event) {
                $shortType = Str::afterLast($event->type, '\\');
                $data = json_decode($event->data, true) ?? [];

                return (object) [
                    'id' => $event->id,
                    'event_type' => $shortType,
                    'event_label' => self::EVENT_LABELS[$shortType] ?? $shortType,
                    'category' => self::EVENT_CATEGORIES[$event->type] ?? 'other',
                    'data' => $data,
                    'description' => $this->formatData($data),
                    'occurred_at' => $event->created_at,
                ];
            });
    }

    public function getTotalCountProperty(): int
    {
        return DB::table('verb_events')->count();
    }

    public function hasMoreProperty(): bool
    {
        return ($this->offset + $this->perPage) < $this->totalCount;
    }

    /** @param array<string, mixed> $data */
    private function formatData(array $data): string
    {
        $parts = [];

        if (! empty($data['student_name'])) {
            $parts[] = (string) $data['student_name'];
        }
        if (! empty($data['offer_name'])) {
            $parts[] = (string) $data['offer_name'];
        }
        if (! empty($data['amount'])) {
            $parts[] = number_format((float) $data['amount'], 0, ',', '.').' kr.';
        }
        if (! empty($data['type'])) {
            $parts[] = (string) $data['type'];
        }
        if (! empty($data['new_status'])) {
            $parts[] = '→ '.(string) $data['new_status'];
        }
        if (! empty($data['starts_at']) && empty($data['new_status'])) {
            $parts[] = Carbon::parse($data['starts_at'])->translatedFormat('j. M Y');
        }
        if (! empty($data['reason'])) {
            $parts[] = 'Årsag: '.(string) $data['reason'];
        }
        if (! empty($data['rejection_reason'])) {
            $parts[] = 'Årsag: '.(string) $data['rejection_reason'];
        }

        return implode(' · ', $parts);
    }

    public function render(): View
    {
        return view('livewire.timeline-feed', [
            'events' => $this->events,
            'hasMore' => $this->hasMore,
        ]);
    }
}
