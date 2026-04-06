# Recurring Theory Sessions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Allow admins to define a recurring theory schedule when creating a course, which auto-generates per-student bookings that appear on the student calendar.

**Architecture:** A new `course_sessions` table stores individual theory session occurrences, generated from a recurrence rule (weekdays + time + end date) stored as JSON on the `courses` table. Each session creates `Booking` rows (type `TheoryLesson`) for enrolled students. Attendance is managed per-session on the course show page.

**Tech Stack:** Laravel 12, Pest 4, Inertia.js v2 + React 19, Tailwind v4

---

### Task 1: Migration — add `theory_schedule` to courses and create `course_sessions` table

**Files:**
- Create: `database/migrations/XXXX_add_theory_schedule_and_course_sessions.php`
- Modify: `app/Models/Course.php`
- Modify: `app/Models/Booking.php`
- Create: `app/Models/CourseSession.php`

- [ ] **Step 1: Create migration**

```bash
php artisan make:migration add_theory_schedule_and_course_sessions --no-interaction
```

- [ ] **Step 2: Write migration content**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->json('theory_schedule')->nullable()->after('public_spots_remaining');
        });

        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->unsignedInteger('session_number');
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'starts_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('course_session_id')->nullable()->after('team_id')->constrained('course_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_session_id');
        });

        Schema::dropIfExists('course_sessions');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('theory_schedule');
        });
    }
};
```

- [ ] **Step 3: Run migration**

```bash
php artisan migrate --no-interaction
```

Expected: Migration runs successfully.

- [ ] **Step 4: Create CourseSession model**

```bash
php artisan make:model CourseSession --no-interaction
```

Write `app/Models/CourseSession.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'starts_at',
        'ends_at',
        'session_number',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'session_number' => 'integer',
            'cancelled_at' => 'datetime',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isCancelled(): bool
    {
        return $this->cancelled_at !== null;
    }
}
```

- [ ] **Step 5: Update Course model** — add `theory_schedule` cast and `sessions()` relation

In `app/Models/Course.php`, add `'theory_schedule'` to `$fillable` and add to `casts()`:

```php
'theory_schedule' => 'array',
```

Add relationship:

```php
public function sessions(): HasMany
{
    return $this->hasMany(CourseSession::class);
}
```

- [ ] **Step 6: Update Booking model** — add `course_session_id` to fillable and add relation

In `app/Models/Booking.php`, add `'course_session_id'` to `$fillable` array. Add relationship:

```php
public function courseSession(): BelongsTo
{
    return $this->belongsTo(CourseSession::class);
}
```

- [ ] **Step 7: Commit**

```bash
git add -A && git commit -m "feat: add course_sessions table, theory_schedule column, and CourseSession model"
```

---

### Task 2: GenerateCourseSessions action

**Files:**
- Create: `app/Actions/Courses/GenerateCourseSessions.php`
- Create: `tests/Feature/Courses/GenerateCourseSessionsTest.php`

- [ ] **Step 1: Write the test**

Create `tests/Feature/Courses/GenerateCourseSessionsTest.php`:

```php
<?php

use App\Actions\Courses\GenerateCourseSessions;
use App\Models\Course;

test('it generates sessions for given weekdays and time range', function () {
    // Monday 2026-04-06 to Sunday 2026-04-19 — schedule Mon+Wed 18:00-20:00
    $course = Course::factory()->create([
        'start_at' => '2026-04-06 09:00:00',
        'end_at' => '2026-04-19 17:00:00',
        'theory_schedule' => [
            'weekdays' => [1, 3], // Mon, Wed
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => '2026-04-19',
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);

    $sessions = $course->sessions()->orderBy('starts_at')->get();

    // Mon 6, Wed 8, Mon 13, Wed 15 = 4 sessions
    expect($sessions)->toHaveCount(4);
    expect($sessions[0]->starts_at->format('Y-m-d H:i'))->toBe('2026-04-06 18:00');
    expect($sessions[0]->ends_at->format('Y-m-d H:i'))->toBe('2026-04-06 20:00');
    expect($sessions[0]->session_number)->toBe(1);
    expect($sessions[1]->starts_at->format('Y-m-d H:i'))->toBe('2026-04-08 18:00');
    expect($sessions[1]->session_number)->toBe(2);
    expect($sessions[3]->session_number)->toBe(4);
});

test('it does not generate sessions when no theory_schedule is set', function () {
    $course = Course::factory()->create(['theory_schedule' => null]);

    app(GenerateCourseSessions::class)->handle($course);

    expect($course->sessions()->count())->toBe(0);
});

test('it clears existing sessions before regenerating', function () {
    $course = Course::factory()->create([
        'start_at' => '2026-04-06 09:00:00',
        'end_at' => '2026-04-19 17:00:00',
        'theory_schedule' => [
            'weekdays' => [1],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => '2026-04-19',
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);
    expect($course->sessions()->count())->toBe(2);

    // Regenerate — should still be 2, not 4
    app(GenerateCourseSessions::class)->handle($course);
    expect($course->sessions()->count())->toBe(2);
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=GenerateCourseSessions
```

Expected: FAIL — class not found.

- [ ] **Step 3: Implement the action**

Create `app/Actions/Courses/GenerateCourseSessions.php`:

```php
<?php

namespace App\Actions\Courses;

use App\Models\Course;
use App\Models\CourseSession;
use Carbon\Carbon;

class GenerateCourseSessions
{
    public function handle(Course $course): void
    {
        $schedule = $course->theory_schedule;

        if (empty($schedule)) {
            return;
        }

        // Clear existing sessions (cascade will remove related bookings via FK)
        $course->sessions()->delete();

        $weekdays = $schedule['weekdays'];
        $startTime = $schedule['start_time'];
        $endTime = $schedule['end_time'];
        $until = Carbon::parse($schedule['until'])->endOfDay();

        $cursor = Carbon::parse($course->start_at)->startOfDay();
        $sessionNumber = 0;

        while ($cursor->lte($until)) {
            // ISO-8601: 1=Monday, 7=Sunday
            if (in_array($cursor->dayOfWeekIso, $weekdays, true)) {
                $sessionNumber++;

                CourseSession::create([
                    'course_id' => $course->id,
                    'starts_at' => $cursor->copy()->setTimeFromTimeString($startTime),
                    'ends_at' => $cursor->copy()->setTimeFromTimeString($endTime),
                    'session_number' => $sessionNumber,
                ]);
            }

            $cursor->addDay();
        }
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --compact --filter=GenerateCourseSessions
```

Expected: 3 tests pass.

- [ ] **Step 5: Commit**

```bash
git add -A && git commit -m "feat: add GenerateCourseSessions action with tests"
```

---

### Task 3: CreateSessionBookings action

**Files:**
- Create: `app/Actions/Courses/CreateSessionBookings.php`
- Create: `tests/Feature/Courses/CreateSessionBookingsTest.php`

- [ ] **Step 1: Write the test**

Create `tests/Feature/Courses/CreateSessionBookingsTest.php`:

```php
<?php

use App\Actions\Courses\CreateSessionBookings;
use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\Student;

test('it creates theory bookings for all enrolled students', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $session = CourseSession::factory()->for($course)->create([
        'starts_at' => now()->addWeek(),
        'ends_at' => now()->addWeek()->addHours(2),
        'session_number' => 1,
    ]);

    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();
    Enrollment::factory()->create([
        'student_id' => $student1->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);
    Enrollment::factory()->create([
        'student_id' => $student2->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(CreateSessionBookings::class)->handle($session);

    $bookings = $session->bookings()->get();
    expect($bookings)->toHaveCount(2);
    expect($bookings[0]->type)->toBe(BookingType::TheoryLesson);
    expect($bookings[0]->status)->toBe(BookingStatus::Scheduled);
    expect($bookings[0]->starts_at->equalTo($session->starts_at))->toBeTrue();
});

test('it does not create duplicate bookings for same student', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $session = CourseSession::factory()->for($course)->create([
        'starts_at' => now()->addWeek(),
        'ends_at' => now()->addWeek()->addHours(2),
        'session_number' => 1,
    ]);

    $student = Student::factory()->create();
    Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(CreateSessionBookings::class)->handle($session);
    app(CreateSessionBookings::class)->handle($session);

    expect($session->bookings()->count())->toBe(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=CreateSessionBookings
```

Expected: FAIL — class not found.

- [ ] **Step 3: Create CourseSession factory**

Create `database/factories/CourseSessionFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseSession>
 */
class CourseSessionFactory extends Factory
{
    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');

        return [
            'course_id' => Course::factory(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+2 hours'),
            'session_number' => 1,
            'cancelled_at' => null,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['cancelled_at' => now()]);
    }
}
```

- [ ] **Step 4: Implement the action**

Create `app/Actions/Courses/CreateSessionBookings.php`:

```php
<?php

namespace App\Actions\Courses;

use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\CourseSession;

class CreateSessionBookings
{
    public function handle(CourseSession $session): void
    {
        $course = $session->course;

        $enrolledStudentIds = $course->enrollments()
            ->where('status', EnrollmentStatus::Completed)
            ->pluck('student_id');

        $existingStudentIds = $session->bookings()
            ->pluck('student_id');

        $newStudentIds = $enrolledStudentIds->diff($existingStudentIds);

        foreach ($newStudentIds as $studentId) {
            Booking::create([
                'student_id' => $studentId,
                'course_session_id' => $session->id,
                'type' => BookingType::TheoryLesson->value,
                'starts_at' => $session->starts_at,
                'ends_at' => $session->ends_at,
            ]);
        }
    }
}
```

- [ ] **Step 5: Run test to verify it passes**

```bash
php artisan test --compact --filter=CreateSessionBookings
```

Expected: 2 tests pass.

- [ ] **Step 6: Commit**

```bash
git add -A && git commit -m "feat: add CreateSessionBookings action with factory and tests"
```

---

### Task 4: SyncNewEnrollmentBookings action + integrate with ApproveEnrollment

**Files:**
- Create: `app/Actions/Courses/SyncNewEnrollmentBookings.php`
- Create: `tests/Feature/Courses/SyncNewEnrollmentBookingsTest.php`
- Modify: `app/Actions/Enrollment/ApproveEnrollment.php`

- [ ] **Step 1: Write the test**

Create `tests/Feature/Courses/SyncNewEnrollmentBookingsTest.php`:

```php
<?php

use App\Actions\Courses\GenerateCourseSessions;
use App\Actions\Courses\SyncNewEnrollmentBookings;
use App\Enums\BookingType;
use App\Enums\EnrollmentStatus;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\Student;

test('it creates bookings for all future uncancelled sessions', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create([
        'start_at' => now()->subDays(3),
        'end_at' => now()->addWeeks(4),
        'theory_schedule' => [
            'weekdays' => [1, 3],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => now()->addWeeks(4)->format('Y-m-d'),
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);
    $totalSessions = $course->sessions()->count();
    $futureSessions = $course->sessions()->where('starts_at', '>', now())->count();

    $student = Student::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(SyncNewEnrollmentBookings::class)->handle($enrollment);

    $bookings = $student->bookings()
        ->where('type', BookingType::TheoryLesson->value)
        ->whereNotNull('course_session_id')
        ->get();

    // Should only get bookings for future sessions, not past ones
    expect($bookings)->toHaveCount($futureSessions);
});

test('it skips cancelled sessions', function () {
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create([
        'start_at' => now(),
        'end_at' => now()->addWeeks(2),
        'theory_schedule' => [
            'weekdays' => [1],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => now()->addWeeks(2)->format('Y-m-d'),
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);

    // Cancel one session
    $course->sessions()->first()->update(['cancelled_at' => now()]);

    $student = Student::factory()->create();
    $enrollment = Enrollment::factory()->create([
        'student_id' => $student->id,
        'offer_id' => $offer->id,
        'course_id' => $course->id,
        'status' => EnrollmentStatus::Completed,
    ]);

    app(SyncNewEnrollmentBookings::class)->handle($enrollment);

    $bookings = $student->bookings()->whereNotNull('course_session_id')->get();
    $futureSessions = $course->sessions()
        ->where('starts_at', '>', now())
        ->whereNull('cancelled_at')
        ->count();

    expect($bookings)->toHaveCount($futureSessions);
});
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=SyncNewEnrollmentBookings
```

Expected: FAIL — class not found.

- [ ] **Step 3: Implement the action**

Create `app/Actions/Courses/SyncNewEnrollmentBookings.php`:

```php
<?php

namespace App\Actions\Courses;

use App\Enums\BookingType;
use App\Models\Booking;
use App\Models\Enrollment;

class SyncNewEnrollmentBookings
{
    public function handle(Enrollment $enrollment): void
    {
        $enrollment->loadMissing('course.sessions');

        $course = $enrollment->course;

        if (! $course) {
            return;
        }

        $futureSessions = $course->sessions()
            ->where('starts_at', '>', now())
            ->whereNull('cancelled_at')
            ->get();

        foreach ($futureSessions as $session) {
            $exists = Booking::where('course_session_id', $session->id)
                ->where('student_id', $enrollment->student_id)
                ->exists();

            if (! $exists) {
                Booking::create([
                    'student_id' => $enrollment->student_id,
                    'course_session_id' => $session->id,
                    'type' => BookingType::TheoryLesson->value,
                    'starts_at' => $session->starts_at,
                    'ends_at' => $session->ends_at,
                ]);
            }
        }
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --compact --filter=SyncNewEnrollmentBookings
```

Expected: 2 tests pass.

- [ ] **Step 5: Integrate with ApproveEnrollment**

In `app/Actions/Enrollment/ApproveEnrollment.php`, add `SyncNewEnrollmentBookings` to the constructor and call it after `createEnrollmentBooking`:

```php
public function __construct(
    private readonly AssignOffer $assignOffer,
    private readonly CreateEnrollmentBooking $createEnrollmentBooking,
    private readonly RecordPayment $recordPayment,
    private readonly SyncNewEnrollmentBookings $syncBookings,
) {}
```

Add after line `$this->createEnrollmentBooking->handle($enrollment);`:

```php
$this->syncBookings->handle($enrollment);
```

Import: `use App\Actions\Courses\SyncNewEnrollmentBookings;`

- [ ] **Step 6: Run full test suite to verify no regressions**

```bash
php artisan test --compact
```

Expected: All tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A && git commit -m "feat: add SyncNewEnrollmentBookings and integrate with enrollment approval"
```

---

### Task 5: CancelCourseSession and RecordSessionAttendance actions

**Files:**
- Create: `app/Actions/Courses/CancelCourseSession.php`
- Create: `app/Actions/Courses/RecordSessionAttendance.php`
- Create: `tests/Feature/Courses/CancelCourseSessionTest.php`
- Create: `tests/Feature/Courses/RecordSessionAttendanceTest.php`

- [ ] **Step 1: Write CancelCourseSession test**

Create `tests/Feature/Courses/CancelCourseSessionTest.php`:

```php
<?php

use App\Actions\Courses\CancelCourseSession;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\CourseSession;
use App\Models\Student;

test('cancelling a session cancels all related bookings', function () {
    $session = CourseSession::factory()->create();
    $student = Student::factory()->create();

    $booking = Booking::factory()->theory()->create([
        'student_id' => $student->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    app(CancelCourseSession::class)->handle($session);

    expect($session->fresh()->cancelled_at)->not->toBeNull();
    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});
```

- [ ] **Step 2: Write RecordSessionAttendance test**

Create `tests/Feature/Courses/RecordSessionAttendanceTest.php`:

```php
<?php

use App\Actions\Courses\RecordSessionAttendance;
use App\Models\Booking;
use App\Models\CourseSession;
use App\Models\Student;

test('it records attendance for specified students', function () {
    $session = CourseSession::factory()->create();
    $s1 = Student::factory()->create();
    $s2 = Student::factory()->create();

    $b1 = Booking::factory()->theory()->create([
        'student_id' => $s1->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);
    $b2 = Booking::factory()->theory()->create([
        'student_id' => $s2->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    app(RecordSessionAttendance::class)->handle($session, [$s1->id]);

    expect($b1->fresh()->attended)->toBeTrue();
    expect($b2->fresh()->attended)->toBeFalse();
});
```

- [ ] **Step 3: Run tests to verify they fail**

```bash
php artisan test --compact --filter="CancelCourseSession|RecordSessionAttendance"
```

Expected: FAIL — classes not found.

- [ ] **Step 4: Implement CancelCourseSession**

Create `app/Actions/Courses/CancelCourseSession.php`:

```php
<?php

namespace App\Actions\Courses;

use App\Enums\BookingStatus;
use App\Models\CourseSession;

class CancelCourseSession
{
    public function handle(CourseSession $session): void
    {
        $session->update(['cancelled_at' => now()]);

        $session->bookings()
            ->where('status', BookingStatus::Scheduled)
            ->update(['status' => BookingStatus::Cancelled]);
    }
}
```

- [ ] **Step 5: Implement RecordSessionAttendance**

Create `app/Actions/Courses/RecordSessionAttendance.php`:

```php
<?php

namespace App\Actions\Courses;

use App\Enums\BookingStatus;
use App\Models\CourseSession;

class RecordSessionAttendance
{
    /**
     * @param  array<int>  $presentStudentIds
     */
    public function handle(CourseSession $session, array $presentStudentIds): void
    {
        $session->bookings()
            ->where('status', '!=', BookingStatus::Cancelled)
            ->each(function ($booking) use ($presentStudentIds) {
                $booking->update([
                    'attended' => in_array($booking->student_id, $presentStudentIds, true),
                    'attendance_recorded_at' => now(),
                ]);
            });
    }
}
```

- [ ] **Step 6: Run tests to verify they pass**

```bash
php artisan test --compact --filter="CancelCourseSession|RecordSessionAttendance"
```

Expected: 2 tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A && git commit -m "feat: add CancelCourseSession and RecordSessionAttendance actions"
```

---

### Task 6: Backend — course store/update + session routes

**Files:**
- Modify: `app/Http/Controllers/Courses/CourseController.php`
- Modify: `app/Http/Requests/Courses/StoreCourseRequest.php`
- Create: `app/Http/Controllers/Courses/CourseSessionController.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/Courses/CourseSessionControllerTest.php`

- [ ] **Step 1: Update StoreCourseRequest validation**

In `app/Http/Requests/Courses/StoreCourseRequest.php`, add to rules:

```php
'theory_weekdays' => ['nullable', 'array'],
'theory_weekdays.*' => ['integer', 'between:1,7'],
'theory_start_time' => ['required_with:theory_weekdays', 'date_format:H:i'],
'theory_end_time' => ['required_with:theory_weekdays', 'date_format:H:i', 'after:theory_start_time'],
'theory_until' => ['required_with:theory_weekdays', 'date', 'after:start_at'],
```

- [ ] **Step 2: Update CourseController::store to generate sessions**

In `app/Http/Controllers/Courses/CourseController.php`, add import:

```php
use App\Actions\Courses\CreateSessionBookings;
use App\Actions\Courses\GenerateCourseSessions;
```

Update `store` method — after `$course = $offer->courses()->create($validated);`, add:

```php
if ($request->filled('theory_weekdays')) {
    $course->update([
        'theory_schedule' => [
            'weekdays' => array_map('intval', $request->input('theory_weekdays')),
            'start_time' => $request->input('theory_start_time'),
            'end_time' => $request->input('theory_end_time'),
            'until' => $request->input('theory_until'),
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);

    $course->sessions->each(function ($session) use ($createSessionBookings) {
        app(CreateSessionBookings::class)->handle($session);
    });
}
```

- [ ] **Step 3: Create CourseSessionController**

Create `app/Http/Controllers/Courses/CourseSessionController.php`:

```php
<?php

namespace App\Http\Controllers\Courses;

use App\Actions\Courses\CancelCourseSession;
use App\Actions\Courses\RecordSessionAttendance;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CourseSessionController extends Controller
{
    public function cancel(Course $course, CourseSession $session, CancelCourseSession $action): RedirectResponse
    {
        $this->authorize('update', $course->offer);

        $action->handle($session);

        return back()->with('success', 'Teoritime aflyst.');
    }

    public function attendance(Request $request, Course $course, CourseSession $session, RecordSessionAttendance $action): RedirectResponse
    {
        $this->authorize('update', $course->offer);

        $validated = $request->validate([
            'present_student_ids' => ['present', 'array'],
            'present_student_ids.*' => ['integer'],
        ]);

        $action->handle($session, $validated['present_student_ids']);

        return back()->with('success', 'Fremmøde registreret.');
    }
}
```

- [ ] **Step 4: Add routes**

In `routes/web.php`, after the `courses.enrollments.attendance` route, add:

```php
Route::post('courses/{course}/sessions/{session}/cancel', [App\Http\Controllers\Courses\CourseSessionController::class, 'cancel'])
    ->name('courses.sessions.cancel');
Route::patch('courses/{course}/sessions/{session}/attendance', [App\Http\Controllers\Courses\CourseSessionController::class, 'attendance'])
    ->name('courses.sessions.attendance');
```

- [ ] **Step 5: Update CourseController::show to include sessions**

In the `show` method of `CourseController`, add `sessions` to the eager load and include in the Inertia response. After `'public_spots_remaining'` add:

```php
'sessions' => $course->sessions()
    ->orderBy('session_number')
    ->get()
    ->map(fn (\App\Models\CourseSession $s) => [
        'id' => $s->id,
        'session_number' => $s->session_number,
        'starts_at' => $s->starts_at->toIso8601String(),
        'ends_at' => $s->ends_at->toIso8601String(),
        'is_cancelled' => $s->isCancelled(),
        'is_past' => $s->starts_at->isPast(),
        'attendance' => $s->bookings->map(fn (\App\Models\Booking $b) => [
            'booking_id' => $b->id,
            'student_id' => $b->student_id,
            'attended' => $b->attended,
        ]),
    ]),
```

Also add `sessions.bookings` to the `$course->load()` call:

```php
$course->load(['offer', 'enrollments.student.user', 'sessions.bookings']);
```

- [ ] **Step 6: Write controller tests**

Create `tests/Feature/Courses/CourseSessionControllerTest.php`:

```php
<?php

use App\Actions\Courses\GenerateCourseSessions;
use App\Enums\BookingStatus;
use App\Enums\EnrollmentStatus;
use App\Models\Booking;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Enrollment;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;

test('admin can cancel a session', function () {
    $admin = User::factory()->create();
    $course = Course::factory()->create();
    $session = CourseSession::factory()->for($course)->create();
    $booking = Booking::factory()->theory()->create([
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    $this->actingAs($admin)
        ->post(route('courses.sessions.cancel', [$course, $session]))
        ->assertRedirect();

    expect($session->fresh()->cancelled_at)->not->toBeNull();
    expect($booking->fresh()->status)->toBe(BookingStatus::Cancelled);
});

test('admin can record session attendance', function () {
    $admin = User::factory()->create();
    $offer = Offer::factory()->create();
    $course = Course::factory()->for($offer)->create();
    $session = CourseSession::factory()->for($course)->create();

    $s1 = Student::factory()->create();
    $s2 = Student::factory()->create();
    $b1 = Booking::factory()->theory()->create([
        'student_id' => $s1->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);
    $b2 = Booking::factory()->theory()->create([
        'student_id' => $s2->id,
        'course_session_id' => $session->id,
        'starts_at' => $session->starts_at,
        'ends_at' => $session->ends_at,
    ]);

    $this->actingAs($admin)
        ->patch(route('courses.sessions.attendance', [$course, $session]), [
            'present_student_ids' => [$s1->id],
        ])
        ->assertRedirect();

    expect($b1->fresh()->attended)->toBeTrue();
    expect($b2->fresh()->attended)->toBeFalse();
});

test('course show includes sessions data', function () {
    $admin = User::factory()->create();
    $course = Course::factory()->create([
        'start_at' => now(),
        'end_at' => now()->addWeeks(2),
        'theory_schedule' => [
            'weekdays' => [1],
            'start_time' => '18:00',
            'end_time' => '20:00',
            'until' => now()->addWeeks(2)->format('Y-m-d'),
        ],
    ]);

    app(GenerateCourseSessions::class)->handle($course);

    $this->actingAs($admin)
        ->get(route('courses.show', $course))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('course.sessions')
        );
});
```

- [ ] **Step 7: Run tests**

```bash
php artisan test --compact --filter="CourseSessionController|CourseAdmin"
```

Expected: All tests pass.

- [ ] **Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 9: Commit**

```bash
git add -A && git commit -m "feat: add course session routes, controller, and course store integration"
```

---

### Task 7: Frontend — theory schedule in course creation form

**Files:**
- Modify: `resources/js/pages/courses/index.tsx`

- [ ] **Step 1: Add theory schedule fields to the create form**

In `resources/js/pages/courses/index.tsx`, update the form state:

```tsx
const createForm = useForm({
    offer_id: '',
    start_at: '',
    end_at: '',
    max_students: '',
    public_spots_remaining: '',
    featured_on_home: false,
    theory_weekdays: [] as number[],
    theory_start_time: '',
    theory_end_time: '',
    theory_until: '',
});
```

- [ ] **Step 2: Add weekday toggle buttons and time fields after the end_at field**

After the end time `</div>` block and before the max students field, add:

```tsx
<div className="space-y-3">
    <Label>Teoritimer (valgfrit)</Label>
    <div className="flex flex-wrap gap-2">
        {[
            { day: 1, label: 'Man' },
            { day: 2, label: 'Tirs' },
            { day: 3, label: 'Ons' },
            { day: 4, label: 'Tors' },
            { day: 5, label: 'Fre' },
            { day: 6, label: 'Lør' },
            { day: 7, label: 'Søn' },
        ].map(({ day, label }) => {
            const selected = createForm.data.theory_weekdays.includes(day);
            return (
                <button
                    key={day}
                    type="button"
                    className={`rounded-md border px-3 py-1.5 text-sm font-medium transition ${
                        selected
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-border bg-background hover:bg-muted'
                    }`}
                    onClick={() => {
                        const current = createForm.data.theory_weekdays;
                        createForm.setData(
                            'theory_weekdays',
                            selected ? current.filter((d) => d !== day) : [...current, day].sort(),
                        );
                    }}
                >
                    {label}
                </button>
            );
        })}
    </div>
    {createForm.data.theory_weekdays.length > 0 && (
        <div className="grid max-w-md grid-cols-3 gap-4">
            <div className="grid gap-2">
                <Label htmlFor="theory_start_time">Fra</Label>
                <Input
                    id="theory_start_time"
                    type="time"
                    value={createForm.data.theory_start_time}
                    onChange={(e) => createForm.setData('theory_start_time', e.target.value)}
                    required
                />
            </div>
            <div className="grid gap-2">
                <Label htmlFor="theory_end_time">Til</Label>
                <Input
                    id="theory_end_time"
                    type="time"
                    value={createForm.data.theory_end_time}
                    onChange={(e) => createForm.setData('theory_end_time', e.target.value)}
                    required
                />
            </div>
            <div className="grid gap-2">
                <Label htmlFor="theory_until">Gentag indtil</Label>
                <Input
                    id="theory_until"
                    type="date"
                    value={createForm.data.theory_until}
                    onChange={(e) => createForm.setData('theory_until', e.target.value)}
                    required
                    min={createForm.data.start_at?.split('T')[0] || undefined}
                />
            </div>
        </div>
    )}
    <p className="text-xs text-muted-foreground">
        Vælg ugedage for at oprette teoritimer automatisk. Elever der tilmelder sig vil få dem i kalenderen.
    </p>
</div>
```

- [ ] **Step 3: Run wayfinder generate**

```bash
php artisan wayfinder:generate --no-interaction
```

- [ ] **Step 4: Verify the form renders correctly**

```bash
npm run build
```

- [ ] **Step 5: Commit**

```bash
git add -A && git commit -m "feat: add theory schedule fields to course creation form"
```

---

### Task 8: Frontend — session list and attendance on course show page

**Files:**
- Modify: `resources/js/pages/courses/show.tsx`

- [ ] **Step 1: Add session types to CourseDetail type**

In `resources/js/pages/courses/show.tsx`, update the `CourseDetail` type:

```tsx
type SessionAttendance = {
    booking_id: number;
    student_id: number;
    attended: boolean | null;
};

type CourseSessionRow = {
    id: number;
    session_number: number;
    starts_at: string;
    ends_at: string;
    is_cancelled: boolean;
    is_past: boolean;
    attendance: SessionAttendance[];
};

type CourseDetail = {
    id: number;
    start_at: string;
    end_at: string;
    max_students: number | null;
    featured_on_home: boolean;
    public_spots_remaining: number | null;
    offer: { id: number; name: string };
    enrollments: Enrollment[];
    sessions: CourseSessionRow[];
};
```

- [ ] **Step 2: Add the sessions section after the enrollments table**

After the enrollments `</div>` closing tag (the `max-w-2xl` div), add a new section:

```tsx
{course.sessions.length > 0 && (
    <div className="max-w-2xl">
        <h2 className="mb-4 text-base font-semibold">
            Teoritimer ({course.sessions.filter((s) => !s.is_cancelled).length})
        </h2>
        <div className="rounded-md border">
            <table className="w-full text-sm">
                <thead>
                    <tr className="border-b bg-muted/50">
                        <th className="px-4 py-2 text-left font-medium">#</th>
                        <th className="px-4 py-2 text-left font-medium">Dato</th>
                        <th className="px-4 py-2 text-left font-medium">Tid</th>
                        <th className="px-4 py-2 text-left font-medium">Fremmøde</th>
                        <th className="px-4 py-2" />
                    </tr>
                </thead>
                <tbody>
                    {course.sessions.map((session) => {
                        const start = new Date(session.starts_at);
                        const end = new Date(session.ends_at);
                        const presentCount = session.attendance.filter((a) => a.attended === true).length;
                        const totalCount = session.attendance.length;

                        return (
                            <tr
                                key={session.id}
                                className={`border-b last:border-0 ${session.is_cancelled ? 'opacity-40 line-through' : ''}`}
                            >
                                <td className="px-4 py-2 font-medium">Teori {session.session_number}</td>
                                <td className="px-4 py-2">
                                    {start.toLocaleDateString('da-DK', { day: 'numeric', month: 'short', year: 'numeric' })}
                                </td>
                                <td className="px-4 py-2">
                                    {start.toLocaleTimeString('da-DK', { hour: '2-digit', minute: '2-digit' })}–
                                    {end.toLocaleTimeString('da-DK', { hour: '2-digit', minute: '2-digit' })}
                                </td>
                                <td className="px-4 py-2">
                                    {session.is_cancelled
                                        ? 'Aflyst'
                                        : totalCount > 0
                                            ? `${presentCount}/${totalCount}`
                                            : '—'}
                                </td>
                                <td className="px-4 py-2 text-right">
                                    {!session.is_cancelled && (
                                        <div className="flex items-center justify-end gap-2">
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                onClick={() => {
                                                    const allStudentIds = session.attendance.map((a) => a.student_id);
                                                    router.patch(
                                                        route('courses.sessions.attendance', { course: course.id, session: session.id }),
                                                        { present_student_ids: allStudentIds },
                                                        { preserveScroll: true },
                                                    );
                                                }}
                                            >
                                                Alle til stede
                                            </Button>
                                            <Form
                                                method="post"
                                                action={route('courses.sessions.cancel', { course: course.id, session: session.id })}
                                                onBefore={() => confirm('Aflys denne teoritime?')}
                                            >
                                                {({ processing }) => (
                                                    <Button type="submit" size="sm" variant="destructive" disabled={processing}>
                                                        Aflys
                                                    </Button>
                                                )}
                                            </Form>
                                        </div>
                                    )}
                                </td>
                            </tr>
                        );
                    })}
                </tbody>
            </table>
        </div>
    </div>
)}
```

- [ ] **Step 3: Add missing imports**

Add `router` to the Inertia import if not present, and ensure `route` is available. Check if `route` is imported from wayfinder routes or use inline URL construction. Based on the existing pattern in the file, use wayfinder-generated route functions. After running `php artisan wayfinder:generate`, import:

```tsx
import { cancel, attendance } from '@/routes/courses/sessions';
```

Update the `router.patch` and `Form` action to use these wayfinder functions instead of `route()`.

- [ ] **Step 4: Run wayfinder and build**

```bash
php artisan wayfinder:generate --no-interaction && npm run build
```

- [ ] **Step 5: Commit**

```bash
git add -A && git commit -m "feat: add theory sessions list with attendance and cancel on course show page"
```

---

### Task 9: Final integration test and cleanup

**Files:**
- Modify: existing tests if needed

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: All tests pass.

- [ ] **Step 2: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 3: Run the full test suite again after pint**

```bash
php artisan test --compact
```

Expected: All tests pass.

- [ ] **Step 4: Final commit and push**

```bash
git add -A && git commit -m "chore: final cleanup for recurring theory sessions" && git push
```
