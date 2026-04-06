<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfferPageMediaController extends Controller
{
    public function store(Request $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,zip,odt,ods,odp,ppt,pptx,xls,xlsx', 'max:51200'],
        ]);

        $page->addMediaFromRequest('file')
            ->toMediaCollection('attachments');

        return back()->with('success', 'Fil uploadet.');
    }
}
