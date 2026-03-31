# Staff/Admin Invite Feature

**Date:** 2026-03-31

## Overview

Admins can create staff members (admins or instructors) by providing a name, email, password, and role. No invitation email is sent — the admin sets the password and shares credentials directly.

## Backend

### Controller: `StaffController`

- `index` — list all non-student users (role = admin or instructor)
- `create` — render the create form
- `store(StoreStaffRequest)` — validate and create `User` with the selected role and hashed password

### Form Request: `StoreStaffRequest`

- Authorization: `$this->user()->isAdmin()`
- Validation rules:
  - `name` — required, string, max 255
  - `email` — required, email, unique:users
  - `password` — required, string, min 8
  - `role` — required, in:admin,instructor

### Routes

Add inside the existing `role:admin` middleware group in `routes/web.php`:

```php
Route::resource('staff', StaffController::class)->only(['index', 'create', 'store']);
```

### No Action Class

The store logic is simple enough (just `User::create`) to live directly in the controller — no separate action class needed.

## Frontend

### Staff Index Page (`resources/js/pages/staff/index.tsx`)

- Table listing all staff users: name, email, role badge
- "Tilføj medarbejder" button linking to the create page
- Uses `AppLayout`

### Staff Create Page (`resources/js/pages/staff/create.tsx`)

- Form fields: name, email, password, role (select: Admin / Instruktør)
- Uses `AppLayout`
- Submit via Inertia `useForm` to `store()` route

### Sidebar

Add "Medarbejdere" to `adminOnlyNavItems` in `app-sidebar.tsx` with `UserCog` icon.

## What This Does NOT Include

- No edit/delete staff functionality (can be added later)
- No invitation email — admin shares credentials directly
- No phone or other profile fields
