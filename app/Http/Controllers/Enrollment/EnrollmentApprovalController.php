<?php

namespace App\Http\Controllers\Enrollment;

use App\Actions\Enrollment\ApproveEnrollment;
use App\Actions\Enrollment\RejectEnrollment;
use App\Enums\EnrollmentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\RejectEnrollmentRequest;
use App\Models\EnrollmentRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentApprovalController extends Controller
{
    public function index(): Response
    {
        $enrollmentRequests = EnrollmentRequest::query()
            ->whereIn('status', [EnrollmentStatus::PendingApproval, EnrollmentStatus::PendingPayment])
            ->with(['student.user', 'offer'])
            ->latest()
            ->get();

        return Inertia::render('enrollments/index', [
            'enrollmentRequests' => $enrollmentRequests->map(fn (EnrollmentRequest $request) => [
                'id' => $request->id,
                'student_name' => $request->student->user->name,
                'student_email' => $request->student->user->email,
                'offer_name' => $request->offer->name,
                'payment_method' => $request->payment_method->value,
                'status' => $request->status->value,
                'created_at' => $request->created_at->toISOString(),
            ]),
        ]);
    }

    public function approve(Request $request, EnrollmentRequest $enrollment, ApproveEnrollment $approveEnrollment): RedirectResponse
    {
        $approveEnrollment->handle($enrollment, $request->user());

        return redirect()->route('enrollments.index')->with('success', 'Tilmelding godkendt.');
    }

    public function reject(RejectEnrollmentRequest $request, EnrollmentRequest $enrollment, RejectEnrollment $rejectEnrollment): RedirectResponse
    {
        $rejectEnrollment->handle($enrollment, $request->validated('rejection_reason'));

        return redirect()->route('enrollments.index')->with('success', 'Tilmelding afvist.');
    }
}
