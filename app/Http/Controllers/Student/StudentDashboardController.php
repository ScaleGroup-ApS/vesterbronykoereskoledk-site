<?php

namespace App\Http\Controllers\Student;

use App\Actions\Student\ComposeStudentPortal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    private function render(string $section, Request $request, ComposeStudentPortal $compose): Response
    {
        $data = match ($section) {
            'forloeb' => $compose->build($request->user(), includePastBookings: true),
            'historik' => $compose->buildHistorik($request->user()),
            'materiale' => $compose->buildMateriale($request->user()),
            'faerdigheder' => $compose->buildFaerdigheder($request->user()),
            default => $compose->build($request->user()),
        };

        return Inertia::render("student/{$section}", $data);
    }

    public function index(Request $request, ComposeStudentPortal $compose): Response
    {
        return $this->render('index', $request, $compose);
    }

    public function forloeb(Request $request, ComposeStudentPortal $compose): Response
    {
        return $this->render('forloeb', $request, $compose);
    }

    public function historik(Request $request, ComposeStudentPortal $compose): Response
    {
        return $this->render('historik', $request, $compose);
    }

    public function materiale(Request $request, ComposeStudentPortal $compose): Response
    {
        return $this->render('materiale', $request, $compose);
    }

    public function faerdigheder(Request $request, ComposeStudentPortal $compose): Response
    {
        return $this->render('faerdigheder', $request, $compose);
    }
}
