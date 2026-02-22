<?php

namespace App\Events;

use App\States\EnrollmentRequestState;
use Thunk\Verbs\Attributes\Autodiscovery\StateId;
use Thunk\Verbs\Event;

class EnrollmentRequested extends Event
{
    #[StateId(EnrollmentRequestState::class)]
    public int $enrollment_request_id;

    public int $student_id;

    public int $offer_id;

    public string $payment_method;

    public function apply(EnrollmentRequestState $state): void
    {
        $state->status = 'pending';
        $state->payment_method = $this->payment_method;
    }
}
