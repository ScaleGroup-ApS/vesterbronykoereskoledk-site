# Manual test plan — Phase A (student progress, calendar, staff deficit, mail)

Use a **local or staging** environment with mail logged (`MAIL_MAILER=log`) or [Mailpit](https://mailpit.axllent.org/) so you can read outbound e-mails without sending real messages.

Prepare:

- One **admin** or **instructor** account.
- One **student** user linked to a **Student** profile, with at least one **Offer** attached (theory/driving counts &gt; 0) via pivot or enrollment flow used in your seed data.
- Optional: a **completed enrollment** with a **Course** (hold) so the student calendar can show the grey “Hold (periode)” span.

---

## A2 — Pakkeforløb (krav / fuldført / planlagt / mangler)

| Step | Action | Expected |
|------|--------|----------|
| 1 | Log in as **student** → **Oversigt** | Section **Dit pakkeforløb** shows compact bars with counts per aktivitet. |
| 2 | Open **Mit forløb** | Table **Dit pakkeforløb** lists Krav, Fuldført, Planlagt, Mangler; footer explains “Mangler”. |
| 3 | As staff, create a **future** booking (e.g. teorilektion) for that student | After save, reload student **Mit forløb**: **Planlagt** increases and **Mangler** decreases for that type (until completed via attendance). |

---

## A1 — Elevkalender

| Step | Action | Expected |
|------|--------|----------|
| 1 | As **student**, open **Kalender** (nav) | Week view loads (da locale); legend shows colours. |
| 2 | | Scheduled bookings appear as coloured blocks; past month still visible if within window. |
| 3 | If student has completed enrollment + course | A long **Hold (periode)** background event appears from course `start_at`–`end_at`. |
| 4 | **Oversigt** | Link **Åbn kalender** goes to the same page. |

---

## A3 — Opret booking (elev deficit)

| Step | Action | Expected |
|------|--------|----------|
| 1 | Log in as **admin/instructor** → **Bookinger** → **Opret booking** | Form loads; no progress block until elev is chosen. |
| 2 | Select an **elev** in the dropdown | Page reloads (partial); **Elevens pakkeforløb** table matches **Mit forløb** numbers for that student. |
| 3 | Change elev | Table updates to the newly selected student. |
| 4 | Open `/bookings/create?student_id={id}` directly | Same student pre-selected and progress visible. |

---

## A4 — Notifikationer (mail + database)

With `MAIL_MAILER=log` or Mailpit:

| Step | Action | Expected |
|------|--------|----------|
| 1 | Create a new booking for a student | Student receives **BookingScheduledNotification** (subject contains app name); body links to **student kalender**. |
| 2 | Drag or patch a **scheduled** booking to new start/end | Student receives **BookingRescheduledNotification**. |
| 3 | Cancel/delete a booking | Student receives **BookingCancelledNotification**; action link points to **kalender** (not staff `/bookings`). |

---

## Regression quick pass

- Run automated tests: `php artisan test --compact tests/Feature/Student tests/Feature/Bookings`
- Staff **Bookinger** index (calendar) still creates/updates bookings as before.
