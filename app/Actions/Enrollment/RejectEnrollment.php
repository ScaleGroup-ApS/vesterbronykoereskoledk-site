<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentStatus;
use App\Events\EnrollmentRejected;
use App\Models\EnrollmentRequest;
use App\Notifications\EnrollmentRejectedNotification;

class RejectEnrollment
{
    public function handle(EnrollmentRequest $enrollmentRequest, string $rejectionReason): EnrollmentRequest
    {
        $enrollmentRequest->load('student.user');

        EnrollmentRejected::fire(
            enrollment_request_id: $enrollmentRequest->id,
            student_id: $enrollmentRequest->student_id,
            offer_id: $enrollmentRequest->offer_id,
            payment_method: $enrollmentRequest->payment_method->value,
            rejection_reason: $rejectionReason,
        );

        $enrollmentRequest->update([
            'status' => EnrollmentStatus::Rejected,
            'rejection_reason' => $rejectionReason,
        ]);

        $enrollmentRequest->student->user->notify(new EnrollmentRejectedNotification($enrollmentRequest));

        return $enrollmentRequest->refresh();
    }
}
