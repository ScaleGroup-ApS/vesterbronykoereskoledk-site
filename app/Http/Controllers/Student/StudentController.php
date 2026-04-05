<?php

namespace App\Http\Controllers\Student;

use App\Actions\Progression\BuildStudentJourney;
use App\Actions\Progression\CheckExamReadiness;
use App\Actions\Student\CreateStudent;
use App\Actions\Student\DeleteStudent;
use App\Actions\Student\SendStudentLoginLink;
use App\Actions\Student\UpdateStudent;
use App\Events\BookingCancelled;
use App\Events\BookingCompleted;
use App\Events\BookingCreated;
use App\Events\BookingNoShow;
use App\Events\EnrollmentApproved;
use App\Events\EnrollmentRejected;
use App\Events\EnrollmentRequested;
use App\Events\OfferAssigned;
use App\Events\PaymentRecorded;
use App\Events\StripePaymentCompleted;
use App\Events\StudentEnrolled;
use App\Events\StudentStatusChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Booking;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        $canEdit = Gate::allows('update', $student);

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
                    'range_label' => $b->starts_at->translatedFormat('d. M Y').' · '.$b->starts_at->format('H:i'),
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

    public function sendLoginLink(Student $student, SendStudentLoginLink $action): RedirectResponse
    {
        $this->authorize('update', $student);

        $student->load('user');
        $action->handle($student);

        return back()->with('success', 'Login link sendt til '.$student->user->name.'.');
    }

    public function storeMedia(Request $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'collection' => ['required', 'string', 'in:documents,photos'],
        ]);

        $student->addMediaFromRequest('file')
            ->toMediaCollection($request->input('collection'));

        return back()->with('success', 'Fil uploadet.');
    }

    private function eventSummary(VerbEvent $event): string
    {
        return match ($event->type) {
            BookingCreated::class => 'Booking oprettet',
            BookingCompleted::class => 'Booking gennemført',
            BookingCancelled::class => 'Booking annulleret',
            BookingNoShow::class => 'Elev mødte ikke op',
            EnrollmentRequested::class => 'Tilmelding anmodet',
            EnrollmentApproved::class => 'Tilmelding godkendt',
            EnrollmentRejected::class => 'Tilmelding afvist',
            StudentEnrolled::class => 'Elev tilmeldt',
            StudentStatusChanged::class => 'Status ændret → '.($event->data['new_status'] ?? ''),
            OfferAssigned::class => 'Tilbud tildelt: '.($event->data['offer_name'] ?? ''),
            PaymentRecorded::class => 'Betaling registreret: '.number_format((float) ($event->data['amount'] ?? 0), 2, ',', '.').' kr.',
            StripePaymentCompleted::class => 'Stripe-betaling gennemført',
            default => class_basename($event->type),
        };
    }

    private function eventCategory(VerbEvent $event): string
    {
        return match ($event->type) {
            BookingCreated::class, BookingCompleted::class, BookingCancelled::class, BookingNoShow::class => 'booking',
            EnrollmentRequested::class, EnrollmentApproved::class, EnrollmentRejected::class => 'enrollment',
            StudentEnrolled::class, StudentStatusChanged::class => 'student',
            OfferAssigned::class, PaymentRecorded::class, StripePaymentCompleted::class => 'payment',
            default => 'other',
        };
    }
}
