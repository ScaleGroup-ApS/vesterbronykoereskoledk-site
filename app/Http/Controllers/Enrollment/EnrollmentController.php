<?php

namespace App\Http\Controllers\Enrollment;

use App\Actions\Offers\AssignOffer;
use App\Enums\StudentStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Models\Offer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class EnrollmentController extends Controller
{
    public function show(Offer $offer): Response
    {
        return Inertia::render('enroll', [
            'offer' => $offer,
        ]);
    }

    public function store(StoreEnrollmentRequest $request, Offer $offer, AssignOffer $assignOffer): RedirectResponse
    {
        $validated = $request->validated();

        $student = DB::transaction(function () use ($validated, $offer, $assignOffer) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::Student,
            ]);

            $student = Student::create([
                'user_id' => $user->id,
                'phone' => $validated['phone'] ?? null,
                'cpr' => $validated['cpr'] ?? null,
                'status' => StudentStatus::Active,
                'start_date' => $validated['start_date'] ?? now()->toDateString(),
            ]);

            $assignOffer->handle($student, $offer);

            return $student;
        });

        Auth::login($student->user);

        return redirect()->route('dashboard');
    }
}
