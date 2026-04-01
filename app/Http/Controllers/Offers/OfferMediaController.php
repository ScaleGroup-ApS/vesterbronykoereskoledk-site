<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OfferMediaController extends Controller
{
    public function store(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $request->validate([
            'file' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,webp,mp4,mov,avi,webm', 'max:2097152'],
        ]);

        $offer->addMediaFromRequest('file')
            ->toMediaCollection('materials');

        return back()->with('success', 'Fil uploadet.');
    }

    public function show(Offer $offer, Media $media): StreamedResponse
    {
        $this->authorize('update', $offer);

        abort_unless((int) $media->model_id === $offer->id, 404);

        return $media->toInlineResponse($media->file_name);
    }

    public function destroy(Offer $offer, Media $media): RedirectResponse
    {
        $this->authorize('update', $offer);

        abort_unless((int) $media->model_id === $offer->id, 404);

        $media->delete();

        return back()->with('success', 'Fil slettet.');
    }
}
