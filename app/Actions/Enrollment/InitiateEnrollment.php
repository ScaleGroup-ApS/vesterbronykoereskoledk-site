<?php

namespace App\Actions\Enrollment;

use App\Enums\EnrollmentPaymentMethod;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Events\EnrollmentRequested;
use App\Events\StudentEnrolled;
use App\Models\EnrollmentRequest;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitiateEnrollment
{
    /**
     * @param  array{name: string, email: string, password: string, phone?: string|null, cpr?: string|null, start_date?: string|null, payment_method: string}  $data
     * @return array{0: Student, 1: EnrollmentRequest}
     */
    public function handle(array $data, Offer $offer): array
    {
        return DB::transaction(function () use ($data, $offer) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::Student,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'phone' => $data['phone'] ?? null,
                'cpr' => $data['cpr'] ?? null,
                'status' => StudentStatus::Active,
                'start_date' => $data['start_date'] ?? now()->toDateString(),
            ]);

            StudentEnrolled::fire(
                student_id: $student->id,
                student_name: $user->name,
                start_date: $student->start_date->toDateString(),
            );

            $paymentMethod = EnrollmentPaymentMethod::from($data['payment_method']);

            $status = $paymentMethod === EnrollmentPaymentMethod::Stripe
                ? EnrollmentStatus::PendingPayment
                : EnrollmentStatus::PendingApproval;

            $enrollmentRequest = EnrollmentRequest::create([
                'student_id' => $student->id,
                'offer_id' => $offer->id,
                'payment_method' => $paymentMethod,
                'status' => $status,
            ]);

            EnrollmentRequested::fire(
                enrollment_request_id: $enrollmentRequest->id,
                student_id: $student->id,
                offer_id: $offer->id,
                payment_method: $paymentMethod->value,
            );

            return [$student, $enrollmentRequest];
        });
    }
}
