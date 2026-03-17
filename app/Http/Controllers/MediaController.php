<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\OfferPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    private const MODELS = [
        'offer-page' => OfferPage::class,
        'offer' => Offer::class,
    ];

    private const FILE_RULES = [
        'images' => ['mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
        'video' => ['mimes:mp4,mov,avi,webm', 'max:2097152'],
        'attachments' => ['mimes:pdf,doc,docx,zip', 'max:51200'],
    ];

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'model_type' => ['required', 'string', Rule::in(array_keys(self::MODELS))],
            'model_id' => ['required', 'integer'],
            'collection' => ['required', 'string', Rule::in(array_keys(self::FILE_RULES))],
            'file' => ['required', 'file'],
        ]);

        $request->validate([
            'file' => array_merge(['required', 'file'], self::FILE_RULES[$validated['collection']]),
        ]);

        $model = self::MODELS[$validated['model_type']]::findOrFail($validated['model_id']);

        $this->authorize('update', $model);

        $model->addMediaFromRequest('file')
            ->toMediaCollection($validated['collection']);

        return back()->with('success', 'Fil uploadet.');
    }

    public function show(Media $media): StreamedResponse
    {
        $model = $media->model;

        abort_unless($model !== null, 404);

        $this->authorize('update', $model);

        return $media->toInlineResponse($media->file_name);
    }

    public function destroy(Media $media): RedirectResponse
    {
        $model = $media->model;

        abort_unless($model !== null, 404);

        $this->authorize('update', $model);

        $media->delete();

        return back()->with('success', 'Fil slettet.');
    }
}
