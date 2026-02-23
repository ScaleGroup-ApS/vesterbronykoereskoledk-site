<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\CalculateKpis;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Offer;
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

        $courseEvents = [];
        $offers = [];

        if ($user->isAdmin() || $user->isInstructor()) {
            $palette = ['#2563eb', '#16a34a', '#dc2626', '#9333ea', '#d97706', '#0891b2'];

            $courses = Course::query()->with('offer')->orderBy('start_at')->get();
            $courseEvents = $courses->map(fn (Course $c) => [
                'id' => (string) $c->id,
                'title' => $c->offer->name,
                'start' => $c->start_at->format('Y-m-d H:i'),
                'end' => $c->end_at->format('Y-m-d H:i'),
                'offer_id' => $c->offer_id,
            ]);

            $offers = Offer::query()->get(['id', 'name'])
                ->values()
                ->map(fn (Offer $o, int $i) => [
                    'id' => $o->id,
                    'name' => $o->name,
                    'color' => $palette[$o->id % count($palette)],
                ]);
        }

        return Inertia::render('dashboard', [
            'kpis' => $kpis->handle($user),
            'pendingEnrollment' => $pendingEnrollment,
            'courseEvents' => $courseEvents,
            'offers' => $offers,
        ]);
    }
}
