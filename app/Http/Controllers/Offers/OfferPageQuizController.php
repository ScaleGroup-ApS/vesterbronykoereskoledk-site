<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Offers\StoreQuizQuestionRequest;
use App\Http\Requests\Offers\UpdateQuizQuestionRequest;
use App\Models\Offer;
use App\Models\OfferModule;
use App\Models\OfferPage;
use App\Models\OfferPageQuizQuestion;
use Illuminate\Http\RedirectResponse;

class OfferPageQuizController extends Controller
{
    public function store(StoreQuizQuestionRequest $request, Offer $offer, OfferModule $module, OfferPage $page): RedirectResponse
    {
        $maxOrder = $page->quizQuestions()->max('sort_order') ?? -1;

        $page->quizQuestions()->create(array_merge($request->validated(), [
            'sort_order' => $maxOrder + 1,
        ]));

        return redirect()->back()
            ->with('success', 'Spørgsmål tilføjet.');
    }

    public function update(UpdateQuizQuestionRequest $request, Offer $offer, OfferModule $module, OfferPage $page, OfferPageQuizQuestion $question): RedirectResponse
    {
        $question->update($request->validated());

        return redirect()->back()
            ->with('success', 'Spørgsmål opdateret.');
    }

    public function destroy(Offer $offer, OfferModule $module, OfferPage $page, OfferPageQuizQuestion $question): RedirectResponse
    {
        $this->authorize('delete', $page);

        $question->delete();

        return redirect()->back()
            ->with('success', 'Spørgsmål slettet.');
    }
}
