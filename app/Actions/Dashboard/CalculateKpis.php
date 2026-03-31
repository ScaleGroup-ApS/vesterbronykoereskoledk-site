<?php

namespace App\Actions\Dashboard;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CalculateKpis
{
    /**
     * @return array<string, mixed>
     */
    public function handle(User $user): array
    {
        if ($user->isAdmin()) {
            return $this->adminKpis();
        }

        if ($user->isInstructor()) {
            return $this->instructorKpis($user);
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function adminKpis(): array
    {
        $row = DB::table('admin_kpis_view')->first();

        $monthlyRevenue = Payment::query()
            ->whereMonth('recorded_at', now()->month)
            ->whereYear('recorded_at', now()->year)
            ->sum('amount');

        $completedThisMonth = Booking::query()
            ->where('status', BookingStatus::Completed)
            ->whereMonth('starts_at', now()->month)
            ->whereYear('starts_at', now()->year)
            ->count();

        $examsPassed = Booking::query()
            ->whereIn('type', [BookingType::TheoryExam, BookingType::PracticalExam])
            ->where('status', BookingStatus::Completed)
            ->where('attended', true)
            ->count();

        $examsTotal = Booking::query()
            ->whereIn('type', [BookingType::TheoryExam, BookingType::PracticalExam])
            ->whereIn('status', [BookingStatus::Completed, BookingStatus::NoShow])
            ->count();

        $examPassRate = $examsTotal > 0
            ? round(($examsPassed / $examsTotal) * 100, 1)
            : 0.0;

        return [
            'total_students' => (int) ($row->total_students ?? 0),
            'upcoming_bookings' => (int) ($row->upcoming_bookings ?? 0),
            'no_show_rate' => (float) ($row->no_show_rate ?? 0.0),
            'total_outstanding' => (float) ($row->total_outstanding ?? 0.0),
            'monthly_revenue' => (float) $monthlyRevenue,
            'completed_this_month' => $completedThisMonth,
            'exam_pass_rate' => $examPassRate,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function instructorKpis(User $instructor): array
    {
        $row = DB::table('instructor_kpis_view')
            ->where('instructor_id', $instructor->id)
            ->first();

        $myStudents = Student::query()
            ->whereHas('bookings', fn ($q) => $q->where('instructor_id', $instructor->id))
            ->count();

        $completedThisMonth = Booking::query()
            ->where('instructor_id', $instructor->id)
            ->where('status', BookingStatus::Completed)
            ->whereMonth('starts_at', now()->month)
            ->whereYear('starts_at', now()->year)
            ->count();

        return [
            'upcoming_bookings' => (int) ($row->upcoming_bookings ?? 0),
            'no_show_rate' => (float) ($row->no_show_rate ?? 0.0),
            'my_students' => $myStudents,
            'completed_this_month' => $completedThisMonth,
        ];
    }
}
