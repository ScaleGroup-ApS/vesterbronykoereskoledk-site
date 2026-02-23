<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\CalculateKpis;
use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CalculateKpis $kpis): Response
    {
        $user = $request->user();

        $pendingEnrollment = null;

        if ($user->isStudent() && $user->student) {
            $pending = Enrollment::query()
                ->where('student_id', $user->student->id)
                ->whereNotIn('status', [EnrollmentStatus::Completed, EnrollmentStatus::Rejected])
                ->latest()
                ->first();

            if ($pending) {
                $pendingEnrollment = [
                    'id' => $pending->id,
                    'status' => $pending->status->value,
                    'payment_method' => $pending->payment_method->value,
                ];
            }
        }

        return Inertia::render('dashboard', [
            'kpis' => $kpis->handle($user),
            'pendingEnrollment' => $pendingEnrollment,
        ]);
    }
}
