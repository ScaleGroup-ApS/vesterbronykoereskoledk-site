<?php

namespace App\Http\Controllers\Curriculum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curriculum\StoreCurriculumTopicRequest;
use App\Http\Requests\Curriculum\UpdateCurriculumTopicRequest;
use App\Models\CurriculumTopic;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumTopicController extends Controller
{
    public function index(Offer $offer): Response
    {
        $this->authorize('update', $offer);

        $topics = $offer->curriculumTopics()->get();

        $materials = $offer->getMedia('materials')->map(fn ($media) => [
            'id' => $media->id,
            'name' => $media->name,
            'size' => $media->human_readable_size,
            'unlock_at_lesson' => $media->getCustomProperty('unlock_at_lesson'),
        ])->values();

        return Inertia::render('curriculum/index', [
            'offer' => $offer->only('id', 'name', 'slug'),
            'topics' => $topics,
            'materials' => $materials,
        ]);
    }

    public function store(StoreCurriculumTopicRequest $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $offer->curriculumTopics()->create($request->validated());

        return back()->with('success', 'Emne tilføjet.');
    }

    public function update(UpdateCurriculumTopicRequest $request, CurriculumTopic $topic): RedirectResponse
    {
        $this->authorize('update', $topic->offer);

        $topic->update($request->validated());

        return back()->with('success', 'Emne opdateret.');
    }

    public function destroy(CurriculumTopic $topic): RedirectResponse
    {
        $this->authorize('update', $topic->offer);

        $topic->delete();

        return back()->with('success', 'Emne slettet.');
    }
}
