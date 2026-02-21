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
    /**
     * @param  array{name: string, email: string, phone?: string, cpr?: string, start_date?: string}  $data
     */
    public function handle(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
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
