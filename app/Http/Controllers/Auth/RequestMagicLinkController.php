<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Student\SendStudentLoginLink;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RequestMagicLinkController extends Controller
{
    /**
     * Handle a student-initiated magic login link request.
     *
     * Always returns success to prevent email enumeration.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->string('email')->lower())
            ->where('role', UserRole::Student)
            ->first();

        if ($user?->student) {
            app(SendStudentLoginLink::class)->handle($user->student);
        }

        return back()->with('status', 'Hvis din e-mail er registreret, modtager du snart et login-link.');
    }
}
