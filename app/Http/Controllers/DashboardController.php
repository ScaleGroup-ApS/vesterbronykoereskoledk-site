<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\CalculateKpis;
use App\Enums\BookingStatus;
use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CalculateKpis $kpis): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        $dayCounts = Booking::query()
            ->selectRaw('DATE(starts_at) as date')
            ->selectRaw(
                'COUNT(DISTINCT CASE WHEN team_id IS NOT NULL '
                .'THEN CONCAT(team_id, "_", starts_at) '
                .'ELSE CAST(id AS CHAR) END) as count'
            )
            ->whereNotIn('status', [BookingStatus::Cancelled->value, BookingStatus::NoShow->value])
            ->groupByRaw('DATE(starts_at)')
            ->orderByRaw('DATE(starts_at)')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'count' => (int) $row->count,
            ])
            ->all();

        $enrollments = [];

        if ($user->isAdmin()) {
            $enrollments = Enrollment::query()
                ->whereIn('status', [EnrollmentStatus::PendingApproval, EnrollmentStatus::PendingPayment])
                ->with(['student.user', 'offer'])
                ->latest()
                ->get()
                ->map(fn (Enrollment $enrollment) => [
                    'id' => $enrollment->id,
                    'student_name' => $enrollment->student->user->name,
                    'student_email' => $enrollment->student->user->email,
                    'offer_name' => $enrollment->offer->name,
                    'payment_method' => $enrollment->payment_method->value,
                    'status' => $enrollment->status->value,
                    'created_at' => $enrollment->created_at->toISOString(),
                ])
                ->all();
        }

        return Inertia::render('dashboard', [
            'kpis' => $kpis->handle($user),
            'dayCounts' => $dayCounts,
            'enrollments' => $enrollments,
        ]);
    }
}
