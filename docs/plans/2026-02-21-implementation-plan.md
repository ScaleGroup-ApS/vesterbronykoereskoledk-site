# Driving School Platform — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a production-ready driving school management platform with students, bookings, payments, progression tracking, and blog.

**Architecture:** Flat domain folders under `app/`. All business logic in Actions (no services). Controllers are thin orchestrators. Policies for authorization. Enums for all status fields. TDD throughout.

**Tech Stack:** Laravel 12, Inertia v2 (React 19), Fortify, MariaDB, Tailwind 4, Pest 4, Wayfinder

---

## Phase 0: Foundation

### Task 0.1: Add Role Enum and Migration

**Files:**
- Create: `app/Enums/UserRole.php`
- Create: `database/migrations/XXXX_add_role_to_users_table.php`
- Modify: `app/Models/User.php`
- Modify: `database/factories/UserFactory.php`
- Test: `tests/Feature/Auth/RoleTest.php`

**Step 1: Create the UserRole enum**

```php
// app/Enums/UserRole.php
<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Instructor = 'instructor';
    case Student = 'student';
}
```

**Step 2: Create migration to add role column**

Run: `php artisan make:migration add_role_to_users_table --table=users --no-interaction`

Edit the migration:
```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('role')->default('admin')->after('email');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
```

**Step 3: Update User model**

Add to `$fillable`: `'role'`
Add to `casts()`: `'role' => UserRole::class`
Add helper methods:
```php
public function isAdmin(): bool
{
    return $this->role === UserRole::Admin;
}

public function isInstructor(): bool
{
    return $this->role === UserRole::Instructor;
}

public function isStudent(): bool
{
    return $this->role === UserRole::Student;
}
```

**Step 4: Update UserFactory**

Add `'role' => UserRole::Admin` to `definition()`.
Add factory states:
```php
public function instructor(): static
{
    return $this->state(fn () => ['role' => UserRole::Instructor]);
}

public function student(): static
{
    return $this->state(fn () => ['role' => UserRole::Student]);
}
```

**Step 5: Update DatabaseSeeder**

Seed an admin user with `'role' => UserRole::Admin`.

**Step 6: Write tests**

```php
// tests/Feature/Auth/RoleTest.php
test('user has admin role by default', function () {
    $user = User::factory()->create();
    expect($user->role)->toBe(UserRole::Admin);
    expect($user->isAdmin())->toBeTrue();
});

test('user can be created as instructor', function () {
    $user = User::factory()->instructor()->create();
    expect($user->role)->toBe(UserRole::Instructor);
    expect($user->isInstructor())->toBeTrue();
});

test('user can be created as student', function () {
    $user = User::factory()->student()->create();
    expect($user->role)->toBe(UserRole::Student);
    expect($user->isStudent())->toBeTrue();
});
```

**Step 7: Run tests**

Run: `php artisan test --compact --filter=RoleTest`
Expected: 3 tests pass

**Step 8: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add UserRole enum and role column to users"
```

---

### Task 0.2: Role Middleware

**Files:**
- Create: `app/Http/Middleware/EnsureUserHasRole.php`
- Modify: `bootstrap/app.php`
- Test: `tests/Feature/Auth/RoleMiddlewareTest.php`

**Step 1: Create middleware**

```php
// app/Http/Middleware/EnsureUserHasRole.php
<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRole = $request->user()?->role;

        if (!$userRole || !in_array($userRole->value, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
```

**Step 2: Register middleware alias in bootstrap/app.php**

Add inside `withMiddleware`:
```php
$middleware->alias([
    'role' => \App\Http\Middleware\EnsureUserHasRole::class,
]);
```

**Step 3: Write tests**

```php
// tests/Feature/Auth/RoleMiddlewareTest.php
use App\Models\User;

test('admin can access admin routes', function () {
    $admin = User::factory()->create(); // default is admin
    $this->actingAs($admin)->get(route('dashboard'))->assertOk();
});

test('unauthenticated user is redirected to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
```

**Step 4: Run tests**

Run: `php artisan test --compact --filter=RoleMiddlewareTest`

**Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add role middleware for route protection"
```

---

### Task 0.3: Audit Log Model

**Files:**
- Create: `app/Models/AuditLog.php`
- Create: `database/migrations/XXXX_create_audit_logs_table.php`
- Create: `database/factories/AuditLogFactory.php`
- Test: `tests/Feature/AuditLogTest.php`

**Step 1: Create model with migration and factory**

Run: `php artisan make:model AuditLog -mf --no-interaction`

**Step 2: Write migration**

```php
public function up(): void
{
    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        $table->string('action'); // created, updated, deleted
        $table->morphs('auditable');
        $table->json('old_values')->nullable();
        $table->json('new_values')->nullable();
        $table->timestamps();
    });
}
```

**Step 3: Write model**

```php
// app/Models/AuditLog.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
```

**Step 4: Write factory**

```php
// database/factories/AuditLogFactory.php
public function definition(): array
{
    return [
        'user_id' => User::factory(),
        'action' => fake()->randomElement(['created', 'updated', 'deleted']),
        'auditable_type' => User::class,
        'auditable_id' => User::factory(),
        'old_values' => null,
        'new_values' => ['name' => fake()->name()],
    ];
}
```

**Step 5: Write tests**

```php
// tests/Feature/AuditLogTest.php
use App\Models\AuditLog;
use App\Models\User;

test('audit log can be created with polymorphic relation', function () {
    $user = User::factory()->create();

    $log = AuditLog::create([
        'user_id' => $user->id,
        'action' => 'updated',
        'auditable_type' => User::class,
        'auditable_id' => $user->id,
        'old_values' => ['name' => 'Old Name'],
        'new_values' => ['name' => 'New Name'],
    ]);

    expect($log->auditable)->toBeInstanceOf(User::class);
    expect($log->user->id)->toBe($user->id);
    expect($log->old_values)->toBe(['name' => 'Old Name']);
    expect($log->new_values)->toBe(['name' => 'New Name']);
});

test('audit log casts json columns correctly', function () {
    $log = AuditLog::factory()->create([
        'new_values' => ['email' => 'test@example.com'],
    ]);

    $log->refresh();
    expect($log->new_values)->toBeArray();
    expect($log->new_values['email'])->toBe('test@example.com');
});
```

**Step 6: Run tests**

Run: `php artisan test --compact --filter=AuditLogTest`

**Step 7: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add AuditLog model with polymorphic relations"
```

---

### Task 0.4: CPR Encryption Cast

**Files:**
- Create: `app/Casts/EncryptedCpr.php`
- Test: `tests/Unit/Casts/EncryptedCprTest.php`

**Step 1: Create the cast**

Run: `php artisan make:cast EncryptedCpr --no-interaction`

```php
// app/Casts/EncryptedCpr.php
<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class EncryptedCpr implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return decrypt($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return encrypt($value);
    }
}
```

Note: Uses Laravel's built-in `encrypt()`/`decrypt()` which uses AES-256-CBC with the APP_KEY. This is simpler and more maintainable than raw openssl. If AES-256-GCM is strictly required, we can swap the implementation later.

**Step 2: Write test**

```php
// tests/Unit/Casts/EncryptedCprTest.php
<?php

use App\Casts\EncryptedCpr;
use Illuminate\Database\Eloquent\Model;

test('encrypts value when setting', function () {
    $cast = new EncryptedCpr();
    $model = Mockery::mock(Model::class);

    $encrypted = $cast->set($model, 'cpr', '010190-1234', []);

    expect($encrypted)->not->toBe('010190-1234');
    expect($encrypted)->toBeString();
});

test('decrypts value when getting', function () {
    $cast = new EncryptedCpr();
    $model = Mockery::mock(Model::class);

    $encrypted = $cast->set($model, 'cpr', '010190-1234', []);
    $decrypted = $cast->get($model, 'cpr', $encrypted, []);

    expect($decrypted)->toBe('010190-1234');
});

test('handles null values', function () {
    $cast = new EncryptedCpr();
    $model = Mockery::mock(Model::class);

    expect($cast->get($model, 'cpr', null, []))->toBeNull();
    expect($cast->set($model, 'cpr', null, []))->toBeNull();
});
```

**Step 3: Run tests**

Run: `php artisan test --compact --filter=EncryptedCprTest`

**Step 4: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add EncryptedCpr cast for CPR number encryption"
```

---

### Task 0.5: Remaining Enums

**Files:**
- Create: `app/Enums/StudentStatus.php`
- Create: `app/Enums/BookingType.php`
- Create: `app/Enums/BookingStatus.php`
- Create: `app/Enums/PaymentMethod.php`
- Test: `tests/Unit/Enums/EnumTest.php`

**Step 1: Create all enums**

```php
// app/Enums/StudentStatus.php
<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Graduated = 'graduated';
    case DroppedOut = 'dropped_out';
}
```

```php
// app/Enums/BookingType.php
<?php

namespace App\Enums;

enum BookingType: string
{
    case DrivingLesson = 'driving_lesson';
    case TheoryLesson = 'theory_lesson';
    case TrackDriving = 'track_driving';
    case SlipperyDriving = 'slippery_driving';
    case Exam = 'exam';
}
```

```php
// app/Enums/BookingStatus.php
<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';
}
```

```php
// app/Enums/PaymentMethod.php
<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case MobilePay = 'mobile_pay';
    case Invoice = 'invoice';
}
```

**Step 2: Write tests**

```php
// tests/Unit/Enums/EnumTest.php
<?php

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\PaymentMethod;
use App\Enums\StudentStatus;
use App\Enums\UserRole;

test('UserRole has expected cases', function () {
    expect(UserRole::cases())->toHaveCount(3);
    expect(UserRole::Admin->value)->toBe('admin');
});

test('StudentStatus has expected cases', function () {
    expect(StudentStatus::cases())->toHaveCount(4);
    expect(StudentStatus::Active->value)->toBe('active');
});

test('BookingType has expected cases', function () {
    expect(BookingType::cases())->toHaveCount(5);
    expect(BookingType::DrivingLesson->value)->toBe('driving_lesson');
});

test('BookingStatus has expected cases', function () {
    expect(BookingStatus::cases())->toHaveCount(4);
    expect(BookingStatus::Scheduled->value)->toBe('scheduled');
});

test('PaymentMethod has expected cases', function () {
    expect(PaymentMethod::cases())->toHaveCount(4);
    expect(PaymentMethod::MobilePay->value)->toBe('mobile_pay');
});
```

**Step 3: Run tests**

Run: `php artisan test --compact --filter=EnumTest`

**Step 4: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add StudentStatus, BookingType, BookingStatus, PaymentMethod enums"
```

---

### Task 0.6: Disable Public Registration

**Files:**
- Modify: `config/fortify.php`
- Modify: `app/Actions/Fortify/CreateNewUser.php`
- Test: `tests/Feature/Auth/RegistrationTest.php` (update existing)

**Step 1: Disable registration in Fortify config**

In `config/fortify.php`, comment out or remove `Features::registration()` from the features array. Admin users will be created via seeder or tinker. Student users will be created by the CreateStudent action.

**Step 2: Update existing registration test**

The existing `tests/Feature/Auth/RegistrationTest.php` tests registration. Update it to verify registration is disabled:

```php
test('registration screen cannot be rendered', function () {
    $response = $this->get('/register');
    $response->assertNotFound();
});
```

**Step 3: Run tests**

Run: `php artisan test --compact --filter=RegistrationTest`

**Step 4: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: disable public registration (users created by admin)"
```

---

### Task 0.7: White-Label Theming System

**Files:**
- Create: `config/branding.php`
- Create: `resources/js/components/theme-provider.tsx`
- Modify: `app/Http/Middleware/HandleInertiaRequests.php` (share branding)
- Modify: `resources/js/layouts/app-layout.tsx` (wrap with ThemeProvider)
- Modify: `.env.example` (add branding vars)
- Test: `tests/Feature/BrandingTest.php`

**Step 1: Create branding config**

```php
// config/branding.php
<?php

return [
    'name' => env('BRAND_NAME', config('app.name')),
    'logo_path' => env('BRAND_LOGO_PATH', null),
    'colors' => [
        'primary' => env('BRAND_COLOR_PRIMARY', null),
        'sidebar' => env('BRAND_COLOR_SIDEBAR', null),
        'accent' => env('BRAND_COLOR_ACCENT', null),
    ],
];
```

**Step 2: Share branding via Inertia middleware**

In `HandleInertiaRequests::share()`, add:
```php
'branding' => [
    'name' => config('branding.name'),
    'logo' => config('branding.logo_path')
        ? asset('storage/' . config('branding.logo_path'))
        : null,
    'colors' => array_filter(config('branding.colors')),
],
```

**Step 3: Create ThemeProvider component**

React component that reads `branding.colors` from shared props and injects CSS custom property overrides via a `<style>` tag. Falls back to defaults if no overrides.

**Step 4: Update app-logo.tsx**

Check for `branding.logo` in shared props. If present, render `<img>`. Otherwise, render the default SVG.

**Step 5: Write test**

```php
// tests/Feature/BrandingTest.php
test('branding config is shared via Inertia', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('branding'));
});
```

**Step 6: Run tests and commit**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=BrandingTest
git add -A && git commit -m "feat: add white-label theming system with config-driven colors"
```

---

## Phase 1: Students Module

### Task 1.1: Student Model, Migration, and Factory

**Files:**
- Create: `app/Models/Student.php`
- Create: `database/migrations/XXXX_create_students_table.php`
- Create: `database/factories/StudentFactory.php`
- Test: `tests/Feature/Students/StudentModelTest.php`

**Step 1: Create model with migration and factory**

Run: `php artisan make:model Student -mf --no-interaction`

**Step 2: Write migration**

```php
public function up(): void
{
    Schema::create('students', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
        $table->string('phone')->nullable();
        $table->text('cpr')->nullable();
        $table->string('status')->default('active');
        $table->date('start_date')->nullable();
        $table->softDeletes();
        $table->timestamps();
    });
}
```

**Step 3: Write Student model**

```php
// app/Models/Student.php
<?php

namespace App\Models;

use App\Casts\EncryptedCpr;
use App\Enums\StudentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'phone',
        'cpr',
        'status',
        'start_date',
    ];

    protected function casts(): array
    {
        return [
            'cpr' => EncryptedCpr::class,
            'status' => StudentStatus::class,
            'start_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
```

**Step 4: Add `student()` relation to User model**

```php
public function student(): HasOne
{
    return $this->hasOne(Student::class);
}
```

**Step 5: Write factory**

```php
// database/factories/StudentFactory.php
public function definition(): array
{
    return [
        'user_id' => User::factory()->student(),
        'phone' => fake()->phoneNumber(),
        'cpr' => fake()->numerify('######-####'),
        'status' => StudentStatus::Active,
        'start_date' => fake()->date(),
    ];
}

public function inactive(): static
{
    return $this->state(fn () => ['status' => StudentStatus::Inactive]);
}

public function graduated(): static
{
    return $this->state(fn () => ['status' => StudentStatus::Graduated]);
}
```

**Step 6: Write tests**

```php
// tests/Feature/Students/StudentModelTest.php
<?php

use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\User;

test('student belongs to a user with student role', function () {
    $student = Student::factory()->create();

    expect($student->user)->toBeInstanceOf(User::class);
    expect($student->user->isStudent())->toBeTrue();
});

test('cpr is encrypted in database and decrypted on access', function () {
    $student = Student::factory()->create(['cpr' => '010190-1234']);

    // Raw DB value should NOT be the plain CPR
    $raw = DB::table('students')->where('id', $student->id)->value('cpr');
    expect($raw)->not->toBe('010190-1234');

    // Model should decrypt it
    $student->refresh();
    expect($student->cpr)->toBe('010190-1234');
});

test('student status is cast to enum', function () {
    $student = Student::factory()->create();
    expect($student->status)->toBeInstanceOf(StudentStatus::class);
    expect($student->status)->toBe(StudentStatus::Active);
});

test('student can be soft deleted', function () {
    $student = Student::factory()->create();
    $student->delete();

    expect(Student::find($student->id))->toBeNull();
    expect(Student::withTrashed()->find($student->id))->not->toBeNull();
});
```

**Step 7: Run tests**

Run: `php artisan test --compact --filter=StudentModelTest`

**Step 8: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add Student model with encrypted CPR and user relation"
```

---

### Task 1.2: Student Policy

**Files:**
- Create: `app/Policies/StudentPolicy.php`
- Test: `tests/Feature/Students/StudentPolicyTest.php`

**Step 1: Create policy**

Run: `php artisan make:policy StudentPolicy --model=Student --no-interaction`

```php
// app/Policies/StudentPolicy.php
<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor();
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin() || $user->isInstructor()) {
            return true;
        }

        return $user->isStudent() && $user->student?->id === $student->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }
}
```

**Step 2: Write tests**

```php
// tests/Feature/Students/StudentPolicyTest.php
<?php

use App\Models\Student;
use App\Models\User;

test('admin can view all students', function () {
    $admin = User::factory()->create();
    expect($admin->can('viewAny', Student::class))->toBeTrue();
});

test('instructor can view all students', function () {
    $instructor = User::factory()->instructor()->create();
    expect($instructor->can('viewAny', Student::class))->toBeTrue();
});

test('student cannot view all students', function () {
    $student = Student::factory()->create();
    expect($student->user->can('viewAny', Student::class))->toBeFalse();
});

test('student can view own profile', function () {
    $student = Student::factory()->create();
    expect($student->user->can('view', $student))->toBeTrue();
});

test('student cannot view other student profile', function () {
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();
    expect($student1->user->can('view', $student2))->toBeFalse();
});

test('only admin can create students', function () {
    $admin = User::factory()->create();
    $instructor = User::factory()->instructor()->create();

    expect($admin->can('create', Student::class))->toBeTrue();
    expect($instructor->can('create', Student::class))->toBeFalse();
});

test('only admin can update students', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();

    expect($admin->can('update', $student))->toBeTrue();
    expect($student->user->can('update', $student))->toBeFalse();
});

test('only admin can delete students', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();

    expect($admin->can('delete', $student))->toBeTrue();
});
```

**Step 3: Run tests**

Run: `php artisan test --compact --filter=StudentPolicyTest`

**Step 4: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add StudentPolicy with role-based access control"
```

---

### Task 1.3: Student CRUD Actions

**Files:**
- Create: `app/Actions/Students/CreateStudent.php`
- Create: `app/Actions/Students/UpdateStudent.php`
- Create: `app/Actions/Students/DeleteStudent.php`
- Test: `tests/Feature/Students/StudentActionsTest.php`

**Step 1: Create actions**

```php
// app/Actions/Students/CreateStudent.php
<?php

namespace App\Actions\Students;

use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateStudent
{
    public function handle(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(32)),
                'role' => UserRole::Student,
            ]);

            return Student::create([
                'user_id' => $user->id,
                'phone' => $data['phone'] ?? null,
                'cpr' => $data['cpr'] ?? null,
                'status' => StudentStatus::Active,
                'start_date' => $data['start_date'] ?? now()->toDateString(),
            ]);
        });
    }
}
```

```php
// app/Actions/Students/UpdateStudent.php
<?php

namespace App\Actions\Students;

use App\Models\Student;

class UpdateStudent
{
    public function handle(Student $student, array $data): Student
    {
        $student->update([
            'phone' => $data['phone'] ?? $student->phone,
            'cpr' => $data['cpr'] ?? $student->cpr,
            'status' => $data['status'] ?? $student->status,
            'start_date' => $data['start_date'] ?? $student->start_date,
        ]);

        $student->user->update([
            'name' => ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''),
            'email' => $data['email'] ?? $student->user->email,
        ]);

        return $student->refresh();
    }
}
```

```php
// app/Actions/Students/DeleteStudent.php
<?php

namespace App\Actions\Students;

use App\Models\Student;

class DeleteStudent
{
    public function handle(Student $student): void
    {
        $student->delete(); // soft delete
    }
}
```

**Step 2: Write tests**

```php
// tests/Feature/Students/StudentActionsTest.php
<?php

use App\Actions\Students\CreateStudent;
use App\Actions\Students\DeleteStudent;
use App\Actions\Students\UpdateStudent;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Models\Student;

test('CreateStudent creates user and student in transaction', function () {
    $action = new CreateStudent();

    $student = $action->handle([
        'first_name' => 'Jonas',
        'last_name' => 'Hansen',
        'email' => 'jonas@example.com',
        'phone' => '+4512345678',
        'cpr' => '010190-1234',
        'start_date' => '2026-03-01',
    ]);

    expect($student)->toBeInstanceOf(Student::class);
    expect($student->user->name)->toBe('Jonas Hansen');
    expect($student->user->email)->toBe('jonas@example.com');
    expect($student->user->role)->toBe(UserRole::Student);
    expect($student->phone)->toBe('+4512345678');
    expect($student->cpr)->toBe('010190-1234');
    expect($student->status)->toBe(StudentStatus::Active);
});

test('UpdateStudent updates student and user data', function () {
    $student = Student::factory()->create();
    $action = new UpdateStudent();

    $updated = $action->handle($student, [
        'first_name' => 'Updated',
        'last_name' => 'Name',
        'email' => 'updated@example.com',
        'phone' => '+4587654321',
    ]);

    expect($updated->user->name)->toBe('Updated Name');
    expect($updated->user->email)->toBe('updated@example.com');
    expect($updated->phone)->toBe('+4587654321');
});

test('DeleteStudent soft deletes student', function () {
    $student = Student::factory()->create();
    $action = new DeleteStudent();

    $action->handle($student);

    expect(Student::find($student->id))->toBeNull();
    expect(Student::withTrashed()->find($student->id))->not->toBeNull();
});
```

**Step 3: Run tests**

Run: `php artisan test --compact --filter=StudentActionsTest`

**Step 4: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add CreateStudent, UpdateStudent, DeleteStudent actions"
```

---

### Task 1.4: Student FormRequests

**Files:**
- Create: `app/Http/Requests/Students/StoreStudentRequest.php`
- Create: `app/Http/Requests/Students/UpdateStudentRequest.php`

**Step 1: Create requests**

Run: `php artisan make:request Students/StoreStudentRequest --no-interaction`
Run: `php artisan make:request Students/UpdateStudentRequest --no-interaction`

```php
// app/Http/Requests/Students/StoreStudentRequest.php
<?php

namespace App\Http\Requests\Students;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone' => ['nullable', 'string', 'max:20'],
            'cpr' => ['nullable', 'string', 'max:11'],
            'start_date' => ['nullable', 'date'],
        ];
    }
}
```

```php
// app/Http/Requests/Students/UpdateStudentRequest.php
<?php

namespace App\Http\Requests\Students;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('student')->user_id;

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'cpr' => ['nullable', 'string', 'max:11'],
            'status' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
        ];
    }
}
```

**Step 2: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add StoreStudentRequest and UpdateStudentRequest"
```

---

### Task 1.5: Student Controller and Routes

**Files:**
- Create: `app/Http/Controllers/Students/StudentController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Students/StudentControllerTest.php`

**Step 1: Create controller**

Run: `php artisan make:controller Students/StudentController --no-interaction`

```php
// app/Http/Controllers/Students/StudentController.php
<?php

namespace App\Http\Controllers\Students;

use App\Actions\Students\CreateStudent;
use App\Actions\Students\DeleteStudent;
use App\Actions\Students\UpdateStudent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StoreStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Student::class);

        $students = Student::with('user')
            ->latest()
            ->paginate(15);

        return Inertia::render('students/index', [
            'students' => $students,
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

    public function show(Student $student): Response
    {
        $this->authorize('view', $student);

        $student->load('user');

        return Inertia::render('students/show', [
            'student' => $student,
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
}
```

**Step 2: Add routes to routes/web.php**

Add inside the auth middleware group:
```php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('students', \App\Http\Controllers\Students\StudentController::class);
});
```

**Step 3: Write tests**

```php
// tests/Feature/Students/StudentControllerTest.php
<?php

use App\Models\Student;
use App\Models\User;

test('admin can view students index', function () {
    $admin = User::factory()->create();
    Student::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('students.index'))
        ->assertOk();
});

test('instructor can view students index', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->get(route('students.index'))
        ->assertOk();
});

test('student cannot view students index', function () {
    $student = Student::factory()->create();

    $this->actingAs($student->user)
        ->get(route('students.index'))
        ->assertForbidden();
});

test('admin can create a student', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('students.store'), [
            'first_name' => 'Jonas',
            'last_name' => 'Hansen',
            'email' => 'jonas@example.com',
            'phone' => '+4512345678',
            'cpr' => '010190-1234',
            'start_date' => '2026-03-01',
        ])
        ->assertRedirect();

    expect(Student::count())->toBe(1);
});

test('admin can update a student', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();

    $this->actingAs($admin)
        ->put(route('students.update', $student), [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => $student->user->email,
        ])
        ->assertRedirect();

    expect($student->fresh()->user->name)->toBe('Updated Name');
});

test('admin can delete a student', function () {
    $admin = User::factory()->create();
    $student = Student::factory()->create();

    $this->actingAs($admin)
        ->delete(route('students.destroy', $student))
        ->assertRedirect(route('students.index'));

    expect(Student::find($student->id))->toBeNull();
});

test('instructor cannot create a student', function () {
    $instructor = User::factory()->instructor()->create();

    $this->actingAs($instructor)
        ->post(route('students.store'), [
            'first_name' => 'Jonas',
            'last_name' => 'Hansen',
            'email' => 'jonas@example.com',
        ])
        ->assertForbidden();
});

test('student can view own profile', function () {
    $student = Student::factory()->create();

    $this->actingAs($student->user)
        ->get(route('students.show', $student))
        ->assertOk();
});

test('student cannot view other student profile', function () {
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();

    $this->actingAs($student1->user)
        ->get(route('students.show', $student2))
        ->assertForbidden();
});
```

**Step 4: Run tests**

Run: `php artisan test --compact --filter=StudentControllerTest`

**Step 5: Run Pint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A && git commit -m "feat: add StudentController with CRUD routes and tests"
```

---

### Task 1.6: Student Inertia Pages

**Files:**
- Create: `resources/js/pages/students/index.tsx`
- Create: `resources/js/pages/students/create.tsx`
- Create: `resources/js/pages/students/edit.tsx`
- Create: `resources/js/pages/students/show.tsx`
- Create: `resources/js/types/student.ts`
- Modify: `resources/js/types/index.ts`
- Modify: `resources/js/components/app-sidebar.tsx`

**Step 1: Add Student TypeScript types**

```tsx
// resources/js/types/student.ts
import type { User } from './auth';

export type Student = {
    id: number;
    user_id: number;
    user: User;
    phone: string | null;
    cpr: string | null;
    status: 'active' | 'inactive' | 'graduated' | 'dropped_out';
    start_date: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
};

export type PaginatedStudents = {
    data: Student[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
};
```

Update `resources/js/types/index.ts` to export student types:
```tsx
export type * from './student';
```

**Step 2: Add Students to sidebar navigation**

In `resources/js/components/app-sidebar.tsx`, add to `mainNavItems`:
```tsx
import { GraduationCap } from 'lucide-react';
// ... import students index route from Wayfinder

{
    title: 'Elever',
    href: /* students.index route from Wayfinder */,
    icon: GraduationCap,
},
```

Note: After adding the route and running `php artisan wayfinder:generate`, import the Wayfinder-generated route function.

**Step 3: Create students/index.tsx**

Use the same patterns from existing pages:
- Import `AppLayout`, `Head`, breadcrumbs pattern
- Display students in a table with name, email, phone, status, actions
- Link to create/show/edit pages
- Use Wayfinder route imports for links

**Step 4: Create students/create.tsx**

- Form with fields: first_name, last_name, email, phone, cpr, start_date
- Use `<Form>` component with Wayfinder action binding
- Use existing Input, Label, Button, InputError components

**Step 5: Create students/edit.tsx**

- Same form as create, pre-populated with student data
- Uses PUT method via Wayfinder

**Step 6: Create students/show.tsx**

- Display student details
- Links to edit/delete

**Step 7: Generate Wayfinder routes**

Run: `php artisan wayfinder:generate`

**Step 8: Commit**

```bash
git add -A && git commit -m "feat: add Student Inertia pages (index, create, edit, show)"
```

---

### Task 1.7: Document Upload

**Files:**
- Create: `app/Models/Document.php`
- Create: `database/migrations/XXXX_create_documents_table.php`
- Create: `database/factories/DocumentFactory.php`
- Create: `app/Http/Controllers/Students/DocumentController.php`
- Create: `app/Http/Requests/Students/StoreDocumentRequest.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Students/DocumentTest.php`

**Step 1: Create model**

Run: `php artisan make:model Document -mf --no-interaction`

Migration:
```php
Schema::create('documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('student_id')->constrained()->cascadeOnDelete();
    $table->string('filename');
    $table->string('path');
    $table->string('mime_type');
    $table->timestamps();
});
```

**Step 2: Write model, controller, request**

Document model with `student()` belongsTo relation.
DocumentController with `store()` and `download()` methods.
StoreDocumentRequest validating file type and size.
Routes nested under students: `students/{student}/documents`.

**Step 3: Write tests**

Test file upload creates document record, test download returns file, test unauthorized access is blocked.

**Step 4: Run tests and commit**

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=DocumentTest
git add -A && git commit -m "feat: add Document model with upload and secure download"
```

---

## Phase 2: Vehicles & Packages

### Task 2.1: Vehicle Model and CRUD

**Files:**
- Create: `app/Models/Vehicle.php` with migration, factory
- Create: `app/Http/Controllers/Vehicles/VehicleController.php`
- Create: `app/Http/Requests/Vehicles/StoreVehicleRequest.php`
- Create: `app/Http/Requests/Vehicles/UpdateVehicleRequest.php`
- Create: `app/Policies/VehiclePolicy.php`
- Create: `resources/js/pages/vehicles/index.tsx`, `create.tsx`, `edit.tsx`
- Test: `tests/Feature/Vehicles/VehicleTest.php`

Vehicle table: `id`, `name`, `plate_number` (unique), `active` (boolean, default true), `timestamps`.

Simple admin-only CRUD. Vehicles are used in bookings.

**Commit:** `feat: add Vehicle model with CRUD`

---

### Task 2.2: Package Model and CRUD

**Files:**
- Create: `app/Models/Package.php` with migration, factory
- Create: `app/Http/Controllers/Offers/PackageController.php`
- Create: `app/Http/Requests/Offers/StorePackageRequest.php`
- Create: `app/Http/Requests/Offers/UpdatePackageRequest.php`
- Create: `app/Policies/PackagePolicy.php`
- Create: `resources/js/pages/packages/index.tsx`, `create.tsx`, `edit.tsx`
- Test: `tests/Feature/Offers/PackageTest.php`

Package table: `id`, `name`, `price` (decimal 10,2), `theory_lessons` (int), `driving_lessons` (int), `track_required` (bool), `slippery_required` (bool), `timestamps`.

Admin-only CRUD.

**Commit:** `feat: add Package model with CRUD`

---

### Task 2.3: Student-Package Assignment

**Files:**
- Create: `database/migrations/XXXX_create_package_student_table.php`
- Create: `app/Actions/Offers/AssignPackage.php`
- Modify: `app/Models/Student.php` (add packages relation)
- Modify: `app/Models/Package.php` (add students relation)
- Test: `tests/Feature/Offers/AssignPackageTest.php`

Pivot table: `student_id`, `package_id`, `assigned_at`.
BelongsToMany on both models.
AssignPackage action handles assignment.

**Commit:** `feat: add student-package assignment with pivot table`

---

## Phase 3: Bookings

### Task 3.1: Booking Model

**Files:**
- Create: `app/Models/Booking.php` with migration, factory
- Test: `tests/Feature/Bookings/BookingModelTest.php`

Booking table: `id`, `student_id` (FK), `instructor_id` (FK → users), `vehicle_id` (FK, nullable), `start_time` (datetime), `end_time` (datetime), `type` (BookingType enum), `status` (BookingStatus enum), `timestamps`.

Relations: belongsTo Student, belongsTo User (instructor), belongsTo Vehicle.

**Commit:** `feat: add Booking model with relations and enum casts`

---

### Task 3.2: Booking Conflict Detection Action

**Files:**
- Create: `app/Actions/Bookings/CheckBookingConflicts.php`
- Test: `tests/Feature/Bookings/BookingConflictTest.php`

The action checks three conflict types:
1. Instructor already booked at overlapping time
2. Vehicle already booked at overlapping time
3. Student already booked at overlapping time

Returns array of conflict descriptions or empty array.

Uses Eloquent scopes on Booking model:
```php
// On Booking model
public function scopeOverlapping(Builder $query, Carbon $start, Carbon $end): Builder
{
    return $query->where('start_time', '<', $end)
        ->where('end_time', '>', $start)
        ->whereNot('status', BookingStatus::Cancelled);
}
```

**Commit:** `feat: add booking conflict detection action`

---

### Task 3.3: Booking CRUD Controller and Routes

**Files:**
- Create: `app/Http/Controllers/Bookings/BookingController.php`
- Create: `app/Http/Requests/Bookings/StoreBookingRequest.php`
- Create: `app/Http/Requests/Bookings/UpdateBookingRequest.php`
- Create: `app/Policies/BookingPolicy.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Bookings/BookingControllerTest.php`

BookingPolicy:
- Admin: full access
- Instructor: view/update own bookings
- Student: view own bookings only

Controller calls `CheckBookingConflicts` before creating/updating. Returns validation error if conflicts found.

**Commit:** `feat: add BookingController with conflict validation`

---

### Task 3.4: Booking Inertia Pages (Calendar)

**Files:**
- Create: `resources/js/pages/bookings/index.tsx` (calendar view)
- Create: `resources/js/pages/bookings/create.tsx`
- Create: `resources/js/types/booking.ts`

Calendar UI using a React calendar library (FullCalendar or custom). Display bookings color-coded by type. Create form with student/instructor/vehicle select, datetime pickers, type select.

Note: May need to install a calendar package (`npm install @fullcalendar/react @fullcalendar/daygrid @fullcalendar/timegrid @fullcalendar/interaction`) — confirm with user before adding dependency.

**Commit:** `feat: add booking calendar UI with Inertia pages`

---

### Task 3.5: Booking Drag & Drop Update

**Files:**
- Modify: `app/Http/Controllers/Bookings/BookingController.php` (update method)
- Modify: `resources/js/pages/bookings/index.tsx` (drag handlers)
- Test: `tests/Feature/Bookings/BookingDragDropTest.php`

Frontend sends PATCH with new start_time/end_time on drag. Backend re-runs conflict detection. Returns 422 if conflicts.

**Commit:** `feat: add drag-and-drop booking updates with conflict re-check`

---

## Phase 4: Payments

### Task 4.1: Payment Model and CRUD

**Files:**
- Create: `app/Models/Payment.php` with migration, factory
- Create: `app/Http/Controllers/Payments/PaymentController.php`
- Create: `app/Http/Requests/Payments/StorePaymentRequest.php`
- Create: `app/Policies/PaymentPolicy.php`
- Create: `app/Actions/Payments/CalculateBalance.php`
- Create: `resources/js/pages/payments/index.tsx`, `create.tsx`
- Test: `tests/Feature/Payments/PaymentTest.php`

Payment table: `id`, `student_id` (FK), `amount` (decimal 10,2), `method` (PaymentMethod enum), `paid_at` (timestamp), `timestamps`.

CalculateBalance action:
```php
public function handle(Student $student): float
{
    $totalOwed = $student->packages()->sum('price');
    $totalPaid = $student->payments()->sum('amount');
    return round($totalOwed - $totalPaid, 2);
}
```

Tests: balance calculation, partial payments, multiple packages.

**Commit:** `feat: add Payment model with balance calculation`

---

## Phase 5: Progression

### Task 5.1: Exam Readiness Action

**Files:**
- Create: `app/Actions/Progression/CheckExamReadiness.php`
- Create: `resources/js/pages/progression/show.tsx`
- Test: `tests/Feature/Progression/ExamReadinessTest.php`

CheckExamReadiness analyzes:
- Count completed bookings by type for the student
- Compare against package requirements (theory_lessons, driving_lessons, track_required, slippery_required)
- Return structured result with `is_ready` boolean and missing items

```php
public function handle(Student $student): array
{
    $package = $student->packages()->latest('package_student.assigned_at')->first();
    if (!$package) {
        return ['is_ready' => false, 'missing' => ['No package assigned']];
    }

    $completed = $student->bookings()
        ->where('status', BookingStatus::Completed)
        ->selectRaw('type, count(*) as count')
        ->groupBy('type')
        ->pluck('count', 'type');

    $missing = [];
    // Compare each requirement against completed counts...

    return [
        'is_ready' => empty($missing),
        'missing' => $missing,
        'completed' => $completed,
    ];
}
```

Tests: student missing lessons not ready, student with all lessons ready, student without package not ready.

**Commit:** `feat: add exam readiness tracking with progression page`

---

## Phase 6: Dashboard

### Task 6.1: KPI Actions and Dashboard

**Files:**
- Create: `app/Actions/Dashboard/CalculateKpis.php`
- Modify: `routes/web.php` (update dashboard route)
- Modify: `resources/js/pages/dashboard.tsx`
- Test: `tests/Feature/Dashboard/KpiTest.php`

CalculateKpis returns:
- `total_students` — active student count
- `upcoming_bookings` — bookings in next 7 days
- `no_show_rate` — percentage of no-show bookings
- `outstanding_balance` — total unpaid across all students

Dashboard page renders KPI cards.

**Commit:** `feat: add KPI dashboard with stats cards`

---

## Phase 7: Blog

### Task 7.1: Blog Model and CRUD

**Files:**
- Create: `app/Models/BlogPost.php` with migration, factory
- Create: `app/Http/Controllers/Blog/BlogPostController.php`
- Create: `app/Http/Requests/Blog/StoreBlogPostRequest.php`
- Create: `app/Http/Requests/Blog/UpdateBlogPostRequest.php`
- Create: `app/Policies/BlogPostPolicy.php`
- Create: `resources/js/pages/blog/index.tsx`, `create.tsx`, `edit.tsx`, `show.tsx`
- Test: `tests/Feature/Blog/BlogPostTest.php`

BlogPost table: `id`, `title`, `slug` (unique), `content` (longtext), `published_at` (nullable timestamp), `seo_description` (nullable string), `timestamps`.

Slug auto-generated from title using `Str::slug()`. Public show route (no auth required). Admin-only CRUD for management.

Tests: slug uniqueness, publish/unpublish, public access.

**Commit:** `feat: add BlogPost model with CRUD and public route`

---

## Phase 8: Reminders

### Task 8.1: Booking Reminder Job

**Files:**
- Create: `app/Jobs/SendBookingReminder.php`
- Create: `app/Notifications/BookingReminderNotification.php`
- Create: `app/Jobs/FlagNoShows.php`
- Modify: `routes/console.php` (schedule)
- Test: `tests/Feature/Reminders/BookingReminderTest.php`

SendBookingReminder: Dispatched by scheduler. Finds bookings starting in 23-25 hours. Sends email notification to student.

FlagNoShows: Finds bookings with status=scheduled where end_time has passed. Updates status to no_show.

Schedule in `routes/console.php`:
```php
Schedule::job(new SendBookingReminder)->hourly();
Schedule::job(new FlagNoShows)->dailyAt('02:00');
```

Tests: reminder dispatched for upcoming booking, no reminder for distant booking, no-show flag applied to past bookings.

**Commit:** `feat: add booking reminder and no-show auto-flag jobs`
