<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentLearnController extends Controller
{
    public function redirectToFirstPage(Request $request, Offer $offer, OfferModule $module): RedirectResponse
    {
        $this->authorize('learnContent', $offer);

        $firstPage = $module->pages()->orderBy('sort_order')->first();

        if (! $firstPage) {
            abort(404);
        }

        return redirect()->route('student.learn.page', [$offer, $module, $firstPage]);
    }
}
