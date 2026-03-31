<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\ComposeStudentPortal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentMaterialeController extends Controller
{
    public function __invoke(Request $request, ComposeStudentPortal $compose): Response
    {
        return Inertia::render('student/materiale', $compose->buildMateriale($request->user()));
    }
}
