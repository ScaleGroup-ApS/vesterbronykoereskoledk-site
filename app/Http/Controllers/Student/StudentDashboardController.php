<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\ComposeStudentPortal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    public function __invoke(Request $request, ComposeStudentPortal $composeStudentPortal): Response
    {
        return Inertia::render('student/index', $composeStudentPortal->build($request->user()));
    }
}
