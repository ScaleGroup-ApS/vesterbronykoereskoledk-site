<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class EnsureAdminUserCommand extends Command
{
    protected $signature = 'admin:ensure-user
                            {--email=test@koreskole.dk : Admin email}
                            {--password=password : Plain password (local development only)}';

    protected $description = 'Create or update the default admin user so you can log in at /login';

    public function handle(): int
    {
        if (! app()->environment('local', 'testing')) {
            $this->error('This command is only available in local and testing environments.');

            return self::FAILURE;
        }

        $email = strtolower((string) $this->option('email'));
        $password = (string) $this->option('password');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Test Bruger',
                'password' => $password,
                'role' => UserRole::Admin,
                'email_verified_at' => now(),
            ],
        );

        $this->components->info("Admin user ready: {$email}");
        $this->line('  Password: '.$password);
        $this->newLine();
        $this->line('Open <fg=cyan>/login</> in the browser using the <fg=yellow>same host</> as <fg=cyan>APP_URL</> in .env');
        $this->line('(e.g. if APP_URL is http://localhost:8000, do not use http://127.0.0.1:8000 — the session cookie will not match.)');

        return self::SUCCESS;
    }
}
