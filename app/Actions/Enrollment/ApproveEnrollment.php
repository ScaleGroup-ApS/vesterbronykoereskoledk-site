<?php

namespace App\Actions\Enrollment;

use App\Actions\Offers\AssignOffer;
use App\Enums\EnrollmentStatus;
use App\Events\EnrollmentApproved;
use App\Models\EnrollmentRequest;
use App\Models\User;
use App\Notifications\EnrollmentApprovedNotification;

class ApproveEnrollment
{
    public function __construct(
        private readonly AssignOffer $assignOffer,
    ) {}

    public function handle(EnrollmentRequest $enrollmentRequest, User $approvedBy): EnrollmentRequest
    {
        $enrollmentRequest->load(['student.user', 'offer']);

        $this->assignOffer->handle($enrollmentRequest->student, $enrollmentRequest->offer);

        EnrollmentApproved::fire(
            enrollment_request_id: $enrollmentRequest->id,
            student_id: $enrollmentRequest->student_id,
            offer_id: $enrollmentRequest->offer_id,
            payment_method: $enrollmentRequest->payment_method->value,
        );

        $enrollmentRequest->update([
            'status' => EnrollmentStatus::Completed,
            'approved_by_id' => $approvedBy->id,
        ]);

        $enrollmentRequest->student->user->notify(new EnrollmentApprovedNotification($enrollmentRequest));

        return $enrollmentRequest->refresh();
    }
}
