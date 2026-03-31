# Køreskole template

Laravel + Inertia (React) application for driving schools (marketing, enrollments, bookings, student portal).

## First-time setup

1. **PHP & Composer** dependencies:

   ```bash
   composer install
   ```

2. **Environment file**:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Edit `.env`: database (`DB_*`), `APP_URL`, mail, Stripe, etc.

3. **Database**:

   ```bash
   php artisan migrate
   ```

4. **JavaScript** (this project uses Bun in `composer.json` scripts; npm works too):

   ```bash
   bun install
   ```

5. **Laravel Wayfinder** (typed routes/actions for the frontend):

   Generated files live under `resources/js/routes`, `resources/js/actions`, and `resources/js/wayfinder`. Those folders are **not committed** (see `.gitignore`), so after every clone or when routes/controllers change you must run:

   ```bash
   php artisan wayfinder:generate
   ```

6. **Frontend dev server** (with Vite):

   ```bash
   bun run dev
   ```

   Or run the full stack (PHP server + queue listener + Vite) as defined in `composer.json`:

   ```bash
   composer run dev
   ```

## After `git pull`

- `composer install` / `bun install` when lockfiles change  
- `php artisan migrate` when there are new migrations  
- `php artisan wayfinder:generate` when routes or invoked controllers used by Wayfinder change  

## Production / staging

### Queue

Many notifications implement `ShouldQueue`. Use a real queue driver in `.env` (not `sync`), e.g. `QUEUE_CONNECTION=database` or `redis`, and run a worker:

```bash
php artisan queue:work
```

(Supervisor, Laravel Horizon, or your host’s queue worker equivalent is recommended.)

### Scheduler

Scheduled jobs are registered in `routes/console.php` (e.g. booking reminders, no-shows). The server must run Laravel’s scheduler every minute:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Without this, time-based jobs will not run.

### Assets

Build front-end assets for deployment:

```bash
bun run build
```

## Manual testing

See `docs/manual-test-plan-phase-a.md` for a checklist around student progress, calendar, staff booking form, and mail notifications.

## Tests

```bash
php artisan test --compact
```
