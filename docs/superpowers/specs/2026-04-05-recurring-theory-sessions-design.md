# Recurring Theory Sessions for Courses

## Summary

When an admin creates a course, they can define a recurring theory schedule (e.g. "every Monday and Wednesday 18:00–20:00 until June 1st"). The system expands this into individual `course_sessions`, and auto-generates `Booking` rows (type `TheoryLesson`) for all enrolled students. Admin can manage attendance per session. New enrollees automatically receive bookings for remaining future sessions.

## Data Model

### New table: `course_sessions`

| Column         | Type             | Notes                              |
|----------------|------------------|------------------------------------|
| id             | bigint PK        |                                    |
| course_id      | FK → courses     |                                    |
| starts_at      | datetime         |                                    |
| ends_at        | datetime         |                                    |
| cancelled_at   | datetime nullable| Soft-cancel a single session       |
| session_number | int              | Auto-numbered (Teori 1, Teori 2…)  |

### Modified table: `courses`

Add `theory_schedule` JSON nullable column:

```json
{
  "weekdays": [1, 3],
  "start_time": "18:00",
  "end_time": "20:00",
  "until": "2026-06-01"
}
```

Weekdays use ISO-8601 (1=Monday, 7=Sunday).

### Bookings

Existing `bookings` table is used as-is. A `Booking` with `type = TheoryLesson` is created per student per session. The `course_session_id` is not needed on bookings — the relationship is derivable via `starts_at`/`ends_at` matching, but for clarity we add:

| Column             | Type             | Notes                        |
|--------------------|------------------|------------------------------|
| course_session_id  | FK nullable      | Links booking to its session |

## Actions

| Action                        | Purpose                                                        |
|-------------------------------|----------------------------------------------------------------|
| `GenerateCourseSessions`      | Expands recurrence rule into `course_sessions` rows            |
| `CreateSessionBookings`       | Creates bookings for all enrolled students for a given session |
| `CancelCourseSession`         | Sets `cancelled_at` on session + cancels related bookings      |
| `RecordSessionAttendance`     | Bulk-updates `attended` on bookings for a session              |
| `SyncNewEnrollmentBookings`   | On enrollment approval — creates bookings for future sessions  |

## Admin UI

### Course creation form (extended)

After existing start/end fields, a new "Teoritimer" section:

- Multi-select weekdays (Man, Tirs, Ons, Tors, Fre, Lør, Søn)
- Start time + end time for sessions (e.g. 18:00–20:00)
- "Gentag indtil" date picker (defaults to course end date)
- On save: `GenerateCourseSessions` → `CreateSessionBookings` for enrolled students

### Course show page (extended)

A new "Teoritimer" section showing all sessions:

| Teori # | Dato          | Tid           | Status      | Fremmøde |
|---------|---------------|---------------|-------------|----------|
| 1       | 6. apr 2026   | 18:00–20:00   | Gennemført  | 12/14    |
| 2       | 8. apr 2026   | 18:00–20:00   | Kommende    | —        |

- Click a session → attendance view with checkboxes per student
- "Marker alle til stede" bulk button
- Cancel single session button (sets `cancelled_at`, cancels bookings)

## Student View

No frontend changes required. Generated `TheoryLesson` bookings automatically appear on:

- Student calendar (FullCalendar)
- Dashboard "Næste aktivitet" countdown
- Progress tracking (lesson progress counts)

## Enrollment Integration

### New student enrolls (enrollment approved/completed):

1. Query `course_sessions` where `starts_at > now()` and `cancelled_at IS NULL`
2. Create a `Booking` (type `TheoryLesson`, status `Scheduled`) per session
3. Student immediately sees all upcoming theory classes

### Session cancelled by admin:

1. Set `cancelled_at` on the `course_session`
2. Set status `Cancelled` on all related bookings

### Admin adds session manually after creation:

1. Create `course_session` row
2. Auto-generate bookings for all currently enrolled students

## Out of Scope

- Editing the recurrence rule after creation (admin can add/cancel individual sessions instead)
- Notification/email when sessions are generated
- Driving lesson scheduling (separate feature)
