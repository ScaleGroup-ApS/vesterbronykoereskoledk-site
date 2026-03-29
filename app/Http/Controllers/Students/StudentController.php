<?php

namespace App\Http\Controllers\Students;

use App\Actions\Progression\BuildStudentJourney;
use App\Actions\Progression\CheckExamReadiness;
use App\Actions\Students\CreateStudent;
use App\Actions\Students\DeleteStudent;
use App\Actions\Students\UpdateStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Booking;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Thunk\Verbs\Models\VerbEvent;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Student::class);

        $query = Student::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');

        $allowedSorts = ['name', 'email', 'status', 'start_date', 'created_at'];
        if (! in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }

        if (in_array($sortField, ['name', 'email'])) {
            $query->join('users', 'students.user_id', '=', 'users.id')
                ->orderBy("users.{$sortField}", $sortDirection)
                ->select('students.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $students = $query->paginate(15)->withQueryString();

        return Inertia::render('students/index', [
            'students' => StudentResource::collection($students),
            'filters' => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
                'sort' => $sortField,
                'direction' => $sortDirection,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Student::class);

        return Inertia::render('students/create');
    }

    public function store(StoreStudentRequest $request, CreateStudent $action): RedirectResponse
    {
        $student = $action->handle($request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Elev oprettet.');
    }

    public function show(
        Request $request,
        Student $student,
        CheckExamReadiness $readiness,
        BuildStudentJourney $buildStudentJourney,
    ): Response {
        $this->authorize('view', $student);

        $student->load(['user', 'media', 'offers']);

        $canEdit = $request->user()->isAdmin();

        return Inertia::render('students/show', [
            'student' => $student,
            'canEdit' => $canEdit,
            'readiness' => $readiness->handle($student),
            'journey' => $buildStudentJourney->handle($student),
            'eventTimeline' => $canEdit
                ? VerbEvent::query()
                    ->where('data->student_id', $student->id)
                    ->latest()
                    ->get()
                    ->map(fn (VerbEvent $event) => [
                        'id' => (string) $event->id,
                        'summary' => $this->eventSummary($event),
                        'category' => $this->eventCategory($event),
                        'created_at' => $event->created_at->toISOString(),
                    ])
                : [],
            'pastBookings' => $student->bookings()
                ->with('instructor:id,name')
                ->orderByDesc('starts_at')
                ->limit(20)
                ->get()
                ->map(fn (Booking $b) => [
                    'id' => $b->id,
                    'type_label' => $b->type->label(),
                    'range_label' => $b->starts_at->translatedFormat('d. MMM yyyy').' · '.$b->starts_at->format('H:i'),
                    'status' => $b->status->value,
                    'attended' => $b->attended,
                    'instructor_note' => $b->instructor_note,
                    'driving_skills' => $b->driving_skills ?? [],
                ])
                ->values()
                ->all(),
        ]);
    }

    public function edit(Student $student): Response
    {
        $this->authorize('update', $student);

        $student->load('user');

        return Inertia::render('students/edit', [
            'student' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student, UpdateStudent $action): RedirectResponse
    {
        $action->handle($student, $request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Elev opdateret.');
    }

    public function destroy(Student $student, DeleteStudent $action): RedirectResponse
    {
        $this->authorize('delete', $student);

        $action->handle($student);

        return redirect()->route('students.index')
            ->with('success', 'Elev slettet.');
    }

    private function eventSummary(VerbEvent $event): string
    {
        return match (class_basename($event->type)) {
            'BookingCreated' => 'Booking oprettet',
            'BookingCompleted' => 'Booking gennemført',
            'BookingCancelled' => 'Booking annulleret',
            'BookingNoShow' => 'Elev mødte ikke op',
            'EnrollmentRequested' => 'Tilmelding anmodet',
            'EnrollmentApproved' => 'Tilmelding godkendt',
            'EnrollmentRejected' => 'Tilmelding afvist',
            'StudentEnrolled' => 'Elev tilmeldt',
            'StudentStatusChanged' => 'Status ændret → '.($event->data['new_status'] ?? ''),
            'OfferAssigned' => 'Tilbud tildelt: '.($event->data['offer_name'] ?? ''),
            'PaymentRecorded' => 'Betaling registreret: '.number_format((float) ($event->data['amount'] ?? 0), 2, ',', '.').' kr.',
            'StripePaymentCompleted' => 'Stripe-betaling gennemført',
            default => class_basename($event->type),
        };
    }

    private function eventCategory(VerbEvent $event): string
    {
        return match (class_basename($event->type)) {
            'BookingCreated', 'BookingCompleted', 'BookingCancelled', 'BookingNoShow' => 'booking',
            'EnrollmentRequested', 'EnrollmentApproved', 'EnrollmentRejected' => 'enrollment',
            'StudentEnrolled', 'StudentStatusChanged' => 'student',
            'OfferAssigned', 'PaymentRecorded', 'StripePaymentCompleted' => 'payment',
            default => 'other',
        };
    }
}
