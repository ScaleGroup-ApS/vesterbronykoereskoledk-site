<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OfferPageVideoController extends Controller
{
    public function store(Request $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $request->validate([
            'file' => ['required', 'file', 'mimes:mp4,mov,avi,webm', 'max:2097152'],
        ]);

        $page->addMediaFromRequest('file')
            ->toMediaCollection('video');

        return back()->with('success', 'Video uploadet.');
    }
}
