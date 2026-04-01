<?php

namespace App\Http\Controllers\Bookings;

use App\Actions\Bookings\CancelBooking;
use App\Actions\Bookings\CreateBooking;
use App\Actions\Bookings\UpdateBooking;
use App\Actions\Student\BuildStudentLessonProgress;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Bookings\StoreBookingRequest;
use App\Http\Requests\Bookings\UpdateBookingRequest;
use App\Models\Booking;
use App\Models\Student;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Booking::class);

        $query = Booking::with(['student.user', 'instructor', 'vehicle']);

        if ($instructorId = request()->input('instructor_id')) {
            $query->where('instructor_id', $instructorId);
        }

        if ($vehicleId = request()->input('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        $bookings = $query->orderBy('starts_at')
            ->get()
            ->map(fn (Booking $booking) => [
                'id' => $booking->id,
                'title' => $booking->student->user->name,
                'start' => $booking->starts_at->toIso8601String(),
                'end' => $booking->ends_at->toIso8601String(),
                'type' => $booking->type->value,
                'status' => $booking->status->value,
                'instructor' => $booking->instructor?->name,
                'instructor_id' => $booking->instructor_id,
                'vehicle' => $booking->vehicle?->name,
                'vehicle_id' => $booking->vehicle_id,
                'notes' => $booking->notes,
                'attended' => $booking->attended,
                'attendance_recorded_at' => $booking->attendance_recorded_at?->toIso8601String(),
                'instructor_note' => $booking->instructor_note,
                'driving_skills' => $booking->driving_skills,
            ]);

        $instructors = User::query()
            ->where('role', 'instructor')
            ->orWhere('role', 'admin')
            ->orderBy('name')
            ->get(['id', 'name']);

        $vehicles = Vehicle::query()
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('bookings/index', [
            'bookings' => $bookings,
            'instructors' => $instructors,
            'vehicles' => $vehicles,
            'filters' => [
                'instructor_id' => request()->input('instructor_id', ''),
                'vehicle_id' => request()->input('vehicle_id', ''),
            ],
        ]);
    }

    public function create(Request $request, BuildStudentLessonProgress $buildStudentLessonProgress): Response
    {
        $this->authorize('create', Booking::class);

        $selectedStudentId = $request->filled('student_id')
            ? $request->integer('student_id')
            : null;

        $studentLessonProgress = null;

        if ($selectedStudentId !== null) {
            $student = Student::find($selectedStudentId);
            if ($student !== null) {
                $studentLessonProgress = $buildStudentLessonProgress->handle($student);
            }
        }

        return Inertia::render('bookings/create', [
            'students' => Student::with('user')->get(),
            'instructors' => User::query()->where('role', 'instructor')->orWhere('role', 'admin')->get(),
            'vehicles' => Vehicle::query()->where('active', true)->get(),
            'bookingTypes' => collect(BookingType::cases())->map(fn (BookingType $t) => [
                'value' => $t->value,
                'label' => $t->label(),
            ]),
            'selectedStudentId' => $selectedStudentId,
            'studentLessonProgress' => $studentLessonProgress,
        ]);
    }

    public function store(StoreBookingRequest $request, CreateBooking $action): RedirectResponse
    {
        $action->handle($request->validated());

        return redirect()->route('bookings.index')->with('success', 'Booking oprettet.');
    }

    public function update(
        UpdateBookingRequest $request,
        Booking $booking,
        UpdateBooking $updateAction,
        CancelBooking $cancelAction,
    ): RedirectResponse {
        $this->authorize('update', $booking);

        $data = $request->validated();

        if (isset($data['status'])) {
            $newStatus = BookingStatus::from($data['status']);

            if ($newStatus === BookingStatus::Completed) {
                return back()->withErrors([
                    'status' => 'Brug «Registrer fremmøde» for at gennemføre lektionen og tælle den med i elevens forløb.',
                ]);
            }

            if ($newStatus === BookingStatus::Cancelled) {
                $cancelAction->handle($booking);
            }

            return back()->with('success', 'Booking opdateret.');
        }

        $updateAction->handle($booking, $data);

        return back()->with('success', 'Booking opdateret.');
    }

    public function destroy(Booking $booking, CancelBooking $action): RedirectResponse
    {
        $this->authorize('delete', $booking);

        $action->handle($booking);

        return redirect()->route('bookings.index')->with('success', 'Booking annulleret.');
    }
}
