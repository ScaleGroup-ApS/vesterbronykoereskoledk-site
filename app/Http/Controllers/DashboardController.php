<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\CalculateKpis;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, CalculateKpis $kpis): Response
    {
        return Inertia::render('dashboard', [
            'kpis' => $kpis->handle($request->user()),
        ]);
    }
}
