# Student Progress & Admin UX — Design Spec
**Date:** 2026-03-28
**Status:** Approved

## Overview

Improve the relationship between admin/staff and students by making progress tracking more realistic and actionable for both sides. Five features are introduced: attendance in the day view, instructor notes per lesson, a fixed theory curriculum, progressive material unlocking, and driving skill tags with a visual for students.

Guiding principle: every feature must make things as easy for admin/staff as for students.

---

## 1. Data Layer

### 1a. `bookings` table — 2 new columns

| Column | Type | Notes |
|--------|------|-------|
| `instructor_note` | `text`, nullable | Written by instructor after a lesson. Visible to the student. |
| `driving_skills` | `json`, nullable | Array of skill keys, e.g. `["parking", "roundabouts"]`. Only set on `driving_lesson` bookings. |

### 1b. New `curriculum_topics` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint PK | |
| `offer_id` | FK → `offers` | Curriculum is per offer/package. |
| `lesson_number` | integer | 1-based. Defines fixed order within the offer. |
| `title` | string | e.g. "Vigepligt og kryds" |
| `description` | text, nullable | Short summary shown to the student. |
| timestamps | | |

A student's completed theory lesson count (bookings of type `theory_lesson` with `status = completed`) determines which curriculum topic they are currently on.

### 1c. Material unlock — Spatie custom properties

No new table. Each media item in an offer's `materials` collection gets an `unlock_at_lesson` custom property (integer). A value of `null` or `0` means always unlocked.

Unlock logic: `unlock_at_lesson <= student's completed theory lesson count`.

### 1d. Driving skills — PHP enum

A new `DrivingSkill` backed enum (no DB table). Initial cases:

`Parkering`, `Motorvej`, `Rundkørsel`, `Bykørsel`, `Overhaling`, `Bakring`, `Filskifte`, `Nødstop`

The enum provides a `label()` method for display. New skills are added to the enum only — no migration needed.

---

## 2. Admin / Staff UI

### 2a. Day view (`bookings/day`) — attendance column

- Add a "Fremmøde" column to the day view table.
- Each row shows an inline checkbox. Checking/unchecking calls the existing `BookingAttendanceController` via an Inertia form with `preserveScroll: true`.
- The checkbox is optimistically updated so staff can check off a full class list quickly without waiting for each response.
- For `driving_lesson` rows, an expandable inline area lets the instructor add a note and select skill tags without leaving the day view.
- For `theory_lesson` rows, only the note field is shown (no skill tags).

### 2b. Booking calendar detail panel (`bookings/index`)

Extends the existing selected-booking panel:

- **Instructor note** — textarea, saves on form submit. Shown for all booking types.
- **Driving skill tags** — multi-select chip picker using the `DrivingSkill` enum. Only rendered for `driving_lesson` type bookings.

### 2c. Student show (`students/show`) — admin view

The past bookings section already exists. Extend it:

- Show instructor note as a soft indented quote below the booking row if present.
- Show skill tag badges on driving lesson rows.

### 2d. Offer admin — new "Læringsplan" tab

A new tab on the offer detail/edit page (or a dedicated route under `/offers/{offer}/curriculum`):

- Lists curriculum topics ordered by `lesson_number`.
- Admin can create, edit, and delete topics (title + description).
- Each topic row has a "Materiale der låses op her" field — sets `unlock_at_lesson` on attached media items.
- Simple list UI, no drag-and-drop required.

### 2e. Course show (`courses/show`)

The enrolled students table gains an attendance summary column:

- Shows `X attended / Y total bookings` per enrolled student.
- Links to the student's profile for drill-down.

---

## 3. Student UI

The student navigation is split from 2 items into 5 dedicated pages.

### 3a. Navigation (`student-layout.tsx`)

| Menu item | Icon | Route |
|-----------|------|-------|
| Oversigt | `LayoutGrid` | `/student` (existing) |
| Mit forløb | `Route` | `/student/forloeb` (refocused) |
| Færdigheder | `Sparkles` | `/student/faerdigheder` (new) |
| Materiale | `BookOpen` | `/student/materiale` (new) |
| Historik | `ClipboardList` | `/student/historik` (new) |

### 3b. Oversigt (refocused)

Adds one new element: the topic of the student's next theory lesson (derived from curriculum + completed lesson count). Shown as a small card below "Næste aktivitet".

### 3c. Mit forløb (refocused)

Now only contains:
- Journey roadmap (existing `StudentJourneyRoadmap` component)
- Exam readiness checklist (existing X/Y table)
- Each theory lesson step in the roadmap shows its curriculum topic title
- Outstanding balance warning (existing)

### 3d. Færdigheder (new page)

- Grid of skill cards, one per `DrivingSkill` enum case.
- Each card: skill name + practice count (e.g. "Rundkørsel × 3").
- Cards are greyed out at 0 practices, progressively lit up as count increases.
- Data comes from aggregating `driving_skills` JSON across the student's completed driving lesson bookings.

### 3e. Materiale (new page)

- Two sections: "Tilgængeligt nu" and "Låst".
- Available materials show name, file size, download link — same as current.
- Locked materials show name and "Låses op efter lektion N" — no download link.
- Unlock state is calculated server-side and passed as a prop.

### 3f. Historik (new page)

- Table of past bookings (moved from "Mit forløb").
- Instructor note rendered as a soft quote below the row when present.
- Driving skill tag badges shown on `driving_lesson` rows.
- Attendance status column retained.

---

## 4. Backend — New Routes & Controllers

| Route | Controller | Notes |
|-------|-----------|-------|
| `GET /student/faerdigheder` | `StudentFaerdighederController` | Aggregates driving skills from bookings |
| `GET /student/materiale` | `StudentMaterialeController` | Materials with unlock state |
| `GET /student/historik` | `StudentHistorikController` | Past bookings with notes + skills |
| `GET /offers/{offer}/curriculum` | `CurriculumTopicController@index` | List topics |
| `POST /offers/{offer}/curriculum` | `CurriculumTopicController@store` | Create topic |
| `PUT /curriculum/{topic}` | `CurriculumTopicController@update` | Edit topic |
| `DELETE /curriculum/{topic}` | `CurriculumTopicController@destroy` | Delete topic |
| `PATCH /bookings/{booking}/note` | `BookingNoteController` | Save instructor note |
| `PATCH /bookings/{booking}/skills` | `BookingSkillsController` | Save driving skills |

The existing `BookingAttendanceController` is reused unchanged — only the day view UI is extended to call it inline.

---

## 5. Out of Scope

- Private vs. public notes toggle (all notes are visible to the student)
- Per-course curriculum overrides (curriculum is per-offer only)
- Quiz or interactive theory content (materials are download-only)
- Skill targets or pass/fail thresholds per skill
- Push notifications when materials unlock
