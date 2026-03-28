<?php

namespace App\Http\Controllers\Marketing\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\UpdateMarketingHomeCopyRequest;
use App\Models\MarketingHomeCopy;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MarketingHomeCopyController extends Controller
{
    public function edit(): Response
    {
        $copy = MarketingHomeCopy::query()->where('key', 'home')->firstOrFail();
        $this->authorize('update', $copy);

        return Inertia::render('marketing/admin/home-copy', [
            'copy' => $copy,
        ]);
    }

    public function update(UpdateMarketingHomeCopyRequest $request): RedirectResponse
    {
        $copy = MarketingHomeCopy::query()->where('key', 'home')->firstOrFail();
        $this->authorize('update', $copy);

        $copy->update($request->validated());

        return redirect()->route('marketing.home-copy.edit')
            ->with('success', 'Forsidetekster er opdateret.');
    }
}
