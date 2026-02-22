<?php

namespace App\Actions\Enrollment;

use App\Actions\Offers\AssignOffer;
use App\Actions\Payments\RecordPayment;
use App\Enums\EnrollmentStatus;
use App\Events\StripePaymentCompleted;
use App\Models\EnrollmentRequest;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CompleteStripeEnrollment
{
    public function __construct(
        private readonly AssignOffer $assignOffer,
        private readonly RecordPayment $recordPayment,
    ) {}

    public function handle(string $sessionId): EnrollmentRequest
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($sessionId);

        if ($session->payment_status !== 'paid') {
            throw ValidationException::withMessages([
                'session' => 'Betalingen er ikke gennemført endnu.',
            ]);
        }

        $enrollmentRequest = EnrollmentRequest::with(['student', 'offer'])
            ->findOrFail($session->metadata->enrollment_request_id);

        if ($enrollmentRequest->status === EnrollmentStatus::Completed) {
            return $enrollmentRequest;
        }

        $this->assignOffer->handle($enrollmentRequest->student, $enrollmentRequest->offer);

        $this->recordPayment->handle([
            'student_id' => $enrollmentRequest->student_id,
            'amount' => $enrollmentRequest->offer->price,
            'method' => 'card',
            'notes' => 'Stripe Checkout Session: '.$sessionId,
        ]);

        StripePaymentCompleted::fire(
            enrollment_request_id: $enrollmentRequest->id,
            student_id: $enrollmentRequest->student_id,
            offer_id: $enrollmentRequest->offer_id,
            payment_method: $enrollmentRequest->payment_method->value,
        );

        $enrollmentRequest->update(['status' => EnrollmentStatus::Completed]);

        return $enrollmentRequest->refresh();
    }
}
