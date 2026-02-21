# Driving School Management Platform — Design Document

**Date:** 2026-02-21
**Stack:** Laravel 12 + Inertia v2 (React 19) + Fortify + MariaDB + Tailwind 4

## Architecture Decisions

### Directory Structure: Flat Domain Folders
All business logic in Actions (no service classes). Controllers are thin orchestrators.

```
app/
├── Actions/{Domain}/       Single-responsibility action classes with handle()
├── Data/                   Simple DTOs for complex flows
├── Enums/                  All enums (UserRole, StudentStatus, BookingType, etc.)
├── Models/                 Eloquent models
├── Policies/               Authorization policies
├── Http/Controllers/       Thin controllers calling Actions
├── Http/Requests/          FormRequest validation
├── Jobs/                   Queued jobs
└── Notifications/          Email notifications
```

### Role System: Enum Column on Users
Three roles: Admin, Instructor, Student. Simple `role` enum column on users table. Middleware for route protection. Policies for fine-grained authorization.

### User + Student Profile
- **User** model handles authentication for all roles.
- **Student** model holds domain data (cpr, phone, status, start_date) linked via `user_id` FK.
- Creating a student creates both a User (role=student) and a Student profile.

## Data Model

### Users
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| name | string | |
| email | string | unique |
| password | string | hashed |
| role | enum | admin/instructor/student |
| email_verified_at | timestamp | nullable |
| two_factor_* | - | Fortify 2FA columns |

### Students
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | bigint | FK → users, unique |
| phone | string | nullable |
| cpr | text | AES-256-GCM encrypted |
| status | enum | active/inactive/graduated/dropped_out |
| start_date | date | nullable |
| deleted_at | timestamp | soft deletes |

### Vehicles
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| name | string | e.g. "Toyota Yaris #1" |
| plate_number | string | unique |
| active | boolean | default true |

### Packages
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| name | string | |
| price | decimal(10,2) | |
| theory_lessons | integer | |
| driving_lessons | integer | |
| track_required | boolean | |
| slippery_required | boolean | |

### Student-Package (pivot)
| Column | Type | Notes |
|--------|------|-------|
| student_id | bigint | FK |
| package_id | bigint | FK |
| assigned_at | timestamp | |

### Bookings
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| student_id | bigint | FK → students |
| instructor_id | bigint | FK → users (instructor) |
| vehicle_id | bigint | FK → vehicles, nullable |
| start_time | datetime | |
| end_time | datetime | |
| type | enum | driving_lesson/theory_lesson/track_driving/slippery_driving/exam |
| status | enum | scheduled/completed/cancelled/no_show |

### Payments
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| student_id | bigint | FK → students |
| amount | decimal(10,2) | |
| method | enum | cash/card/mobile_pay/invoice |
| paid_at | timestamp | |

### Documents
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| student_id | bigint | FK → students |
| filename | string | original filename |
| path | string | storage path |
| mime_type | string | |

### Blog Posts
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| title | string | |
| slug | string | unique |
| content | longtext | |
| published_at | timestamp | nullable |
| seo_description | string | nullable |

### Audit Logs
| Column | Type | Notes |
|--------|------|-------|
| id | bigint | PK |
| user_id | bigint | FK → users, nullable |
| action | string | created/updated/deleted |
| auditable_type | string | polymorphic |
| auditable_id | bigint | polymorphic |
| old_values | json | nullable |
| new_values | json | nullable |

## Enums

- **UserRole:** Admin, Instructor, Student
- **StudentStatus:** Active, Inactive, Graduated, DroppedOut
- **BookingType:** DrivingLesson, TheoryLesson, TrackDriving, SlipperyDriving, Exam
- **BookingStatus:** Scheduled, Completed, Cancelled, NoShow
- **PaymentMethod:** Cash, Card, MobilePay, Invoice

## Key Business Rules

1. **Booking conflicts:** No instructor or vehicle double-booked for overlapping times. Students cannot have overlapping bookings.
2. **CPR encryption:** AES-256-GCM via custom Eloquent cast. Key derived from APP_KEY.
3. **Exam readiness:** Student must have completed all required lesson types from their package before being marked ready.
4. **Balance calculation:** Sum of package prices minus sum of payments.
5. **Role access:** Admin sees everything. Instructor sees own bookings + assigned students. Student sees own data only.
6. **No-show auto-flag:** Scheduled job checks past bookings not marked completed/cancelled.

## Implementation Phases

### Phase 0 — Foundation
- 0.1: Auth & Roles (role enum, middleware, policies, admin seed)
- 0.2: Base Architecture (AuditLog, enums, queue config, CPR encryption cast)

### Phase 1 — Students
- 1.1: Student Entity (model, migration, CRUD actions, policy, Inertia pages)
- 1.2: Document Upload (model, storage, secure download)

### Phase 2 — Vehicles & Packages
- 2.1: Vehicle Entity (simple CRUD)
- 2.2: Package Entity (CRUD, assignment pivot, lesson credit generation)

### Phase 3 — Bookings
- 3.1: Booking Entity (model, conflict detection, CRUD, calendar UI)
- 3.2: Drag & Drop Update (re-validation on move)

### Phase 4 — Payments
- 4.1: Payment Entity (CRUD, balance calculation)

### Phase 5 — Progression
- 5.1: Module Tracking (completion tracking, exam readiness indicator)

### Phase 6 — Dashboard
- 6.1: KPI Actions (pass rate, no-show rate, upcoming bookings, balances)

### Phase 7 — Blog
- 7.1: Blog Entity (CRUD, slug generation, public route)

### Phase 8 — Reminders
- 8.1: Booking Reminder Job (24h email, no-show auto-flag)

Each phase follows: Implement → Test → Run tests → Refactor → Commit.

## White-Label / Theming Strategy

This application is white-labelled — each deployment represents a different driving school with its own branding.

### What's Configurable Per Deployment

1. **Brand colors** — Primary, accent, and sidebar colors via CSS custom properties (already using oklch tokens in `app.css`)
2. **Logo** — App logo component (`app-logo.tsx`, `app-logo-icon.tsx`) loads from storage or config
3. **App name** — Already configurable via `config('app.name')`
4. **Font** — Optional override of `--font-sans` CSS variable

### Implementation Approach

- **Config-driven theming**: Add `config/branding.php` with color overrides, logo path, font choice
- **CSS injection**: `HandleInertiaRequests` middleware shares branding config; a `<ThemeProvider>` component generates CSS custom property overrides from the config
- **No rebuild required**: Colors are CSS variables, so changing `.env` values regenerates the theme without rebuilding frontend assets
- **Logo**: Stored in `storage/app/public/branding/` — fallback to default SVG if not present
- **Dark mode**: Both light and dark variants configurable per brand
