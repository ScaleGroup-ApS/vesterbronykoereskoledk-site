<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function show(Offer $offer, OfferModule $module, OfferPage $page, Media $media): StreamedResponse
    {
        $this->authorize('update', $page);

        abort_unless((int) $media->model_id === $page->id, 404);

        return $media->toInlineResponse($media->file_name);
    }

    public function destroy(Offer $offer, OfferModule $module, OfferPage $page, Media $media): RedirectResponse
    {
        $this->authorize('update', $page);

        abort_unless((int) $media->model_id === $page->id, 404);

        $media->delete();

        return back()->with('success', 'Fil slettet.');
    }
}
