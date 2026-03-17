<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Offers\StoreOfferPageRequest;
use App\Http\Requests\Offers\UpdateOfferPageRequest;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OfferPageController extends Controller
{
    public function store(StoreOfferPageRequest $request, Offer $offer, OfferModule $module): RedirectResponse
    {
        $maxOrder = $module->pages()->max('sort_order') ?? -1;

        $module->pages()->create([
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('offers.modules.index', $offer)
            ->with('success', 'Side oprettet.');
    }

    public function edit(Offer $offer, OfferModule $module, OfferPage $page): Response
    {
        $this->authorize('update', $page);

        $page->load('quizQuestions');

        $attachments = $page->getMedia('attachments')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'size' => $media->human_readable_size,
        ])->values()->all();

        $images = $page->getMedia('images')->map(fn ($media) => [
            'id' => $media->id,
            'file_name' => $media->file_name,
            'size' => $media->human_readable_size,
        ])->values()->all();

        $videos = $page->getMedia('video')->map(fn ($media) => [
            'id' => $media->id,
            'file_name' => $media->file_name,
            'size' => $media->human_readable_size,
            'processing' => ! $media->hasGeneratedConversion('thumbnail'),
            'thumbnail_url' => $media->hasGeneratedConversion('thumbnail')
                ? $media->getUrl('thumbnail')
                : null,
        ])->values()->all();

        return Inertia::render('offers/pages/edit', [
            'offer' => $offer,
            'module' => $module,
            'page' => array_merge($page->toArray(), ['attachments' => $attachments]),
            'images' => $images,
            'videos' => $videos,
        ]);
    }

    public function update(UpdateOfferPageRequest $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $page->update([
            'title' => $request->validated('title'),
            'body' => $request->validated('body'),
        ]);

        return redirect()->route('offers.modules.index', $offer)
            ->with('success', 'Side opdateret.');
    }

    public function destroy(Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('delete', $page);

        $page->delete();

        return redirect()->route('offers.modules.index', $offer)
            ->with('success', 'Side slettet.');
    }

    public function moveUp(Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $previous = $module->pages()
            ->where('sort_order', '<', $page->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previous) {
            [$page->sort_order, $previous->sort_order] = [$previous->sort_order, $page->sort_order];
            $page->save();
            $previous->save();
        }

        return redirect()->route('offers.modules.index', $offer);
    }

    public function moveDown(Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $next = $module->pages()
            ->where('sort_order', '>', $page->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$page->sort_order, $next->sort_order] = [$next->sort_order, $page->sort_order];
            $page->save();
            $next->save();
        }

        return redirect()->route('offers.modules.index', $offer);
    }
}
