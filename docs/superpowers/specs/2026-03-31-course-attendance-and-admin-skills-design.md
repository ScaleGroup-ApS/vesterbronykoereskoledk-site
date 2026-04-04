# Course Attendance Toggle & Admin Færdigheder

**Date:** 2026-03-31

## Overview

Two features:
1. Admins can toggle student attendance directly on the course show page via checkboxes.
2. Admins can toggle driving skills (færdigheder) on the student show page, independent of bookings.

---

## Feature 1: Course Attendance Toggle

### Database

New migration: add `attended` column to `enrollments` table.
- `attended` — boolean, nullable, default null

### Backend

**New controller:** `CourseAttendanceController` (single invokable or `__invoke`)
- Route: `PATCH /courses/{course}/enrollments/{enrollment}/attendance`
- Toggles `enrollment.attended` (null/false → true, true → false)
- Authorization: admin only
- Returns redirect back

**Progression counting:** The `attended_count` in `CourseController@show` currently counts completed bookings. Update it to also include enrollments where `attended = true`. Anywhere else that counts attendance for progression (e.g. `ComposeStudentPortal`, `CheckExamReadiness`, `BuildStudentLessonProgress`) should also factor in enrollment attendance.

### Frontend

On `courses/show.tsx`, in the enrollments table:
- Replace the read-only "Fremmøde" text (`0 / 1`) with a clickable Checkbox
- On change, send PATCH to the new endpoint
- Checkbox is checked when `enrollment.attended === true`
- Add `attended` field to the `Enrollment` type

---

## Feature 2: Admin Færdigheder on Student Show Page

### Database

New migration: add `completed_skills` column to `students` table.
- `completed_skills` — JSON, nullable, default null
- Stores array of DrivingSkill enum values, e.g. `["parking", "city_driving"]`

### Backend

**New controller:** `StudentSkillController`
- Route: `PATCH /students/{student}/skills`
- Receives `{ skills: string[] }` — array of DrivingSkill enum values
- Saves to `student.completed_skills`
- Authorization: admin only
- Returns redirect back

**Student model:** Add `completed_skills` to `$fillable` and cast it as `array` in `casts()`.

**StudentController@show:** Pass `completed_skills` from the student to the frontend (already loading student, just need to include the field).

### Frontend

On `students/show.tsx`, add a new "Færdigheder" section (after the info grid, before documents):
- Display all 8 DrivingSkill items as toggle pill buttons
- Highlighted/filled = skill is in `completed_skills`, outline = not
- On click, immediately send PATCH with the updated skills array
- Uses the same `skillLabels` map already defined in the file

### Student-facing

The student færdigheder page (`student/faerdigheder.tsx`) continues showing booking-based practice counts as before. Additionally, skills present in `completed_skills` get a checkmark or "Godkendt" badge to show admin sign-off.

---

## What This Does NOT Include

- No changes to the existing booking-level skill tracking (BookingSkillsController)
- No changes to how bookings record attendance (BookingAttendanceController)
- No new sidebar items or pages — both features are additions to existing pages
