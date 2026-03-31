<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfferPageBannerController extends Controller
{
    public function store(Request $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
        ]);

        $page->addMediaFromRequest('file')
            ->toMediaCollection('banner');

        return back()->with('success', 'Bannerbillede uploadet.');
    }

    public function show(Offer $offer, OfferModule $module, OfferPage $page): StreamedResponse
    {
        $this->authorize('update', $page);

        $media = $page->getFirstMedia('banner') ?? abort(404);

        return $media->toInlineResponse($media->file_name);
    }

    public function destroy(Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $page->clearMediaCollection('banner');

        return back()->with('success', 'Bannerbillede slettet.');
    }
}
