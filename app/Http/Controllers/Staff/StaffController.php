<?php

namespace App\Http\Controllers\Staff;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StoreStaffRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    public function index(): Response
    {
        $staff = User::query()
            ->whereIn('role', [UserRole::Admin, UserRole::Instructor])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        return Inertia::render('staff/index', [
            'staff' => $staff,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('staff/create');
    }

    public function store(StoreStaffRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('staff.index')
            ->with('success', 'Medarbejder oprettet.');
    }
}
