<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(Request $request): StreamedResponse
    {
        $media = Media::findOrFail($request->route('media'));

        // Generic checks (trashed owner, expiry, etc.)
        Gate::authorize('view', $media);

        // Owner-specific access rules
        Gate::authorize('download', [$media->model, $media]);

        return $media->toInlineResponse($request);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $media = Media::findOrFail($request->route('media'));

        Gate::authorize('delete', $media);

        $media->delete();

        return back()->with('success', 'Fil slettet.');
    }
}
