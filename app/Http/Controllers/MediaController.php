<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(Request $request): StreamedResponse
    {
        $media = Media::findOrFail($request->route('media'));

        $this->authorize('view', $media);

        return $media->toInlineResponse($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $media = Media::findOrFail($request->route('media'));

        $this->authorize('delete', $media);

        $media->delete();

        return back()->with('success', 'Fil slettet.');
    }
}
