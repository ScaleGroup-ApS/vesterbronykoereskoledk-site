<?php

namespace App\Http\Controllers\Marketing\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\UpdateMarketingContactDetailRequest;
use App\Models\MarketingContactDetail;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MarketingContactDetailController extends Controller
{
    public function edit(): Response
    {
        $detail = MarketingContactDetail::singleton();
        $this->authorize('update', $detail);

        return Inertia::render('marketing/admin/contact-details', [
            'detail' => $detail,
        ]);
    }

    public function update(UpdateMarketingContactDetailRequest $request): RedirectResponse
    {
        $detail = MarketingContactDetail::singleton();
        $this->authorize('update', $detail);

        $detail->update($request->validated());

        return redirect()->route('marketing.contact-details.edit')
            ->with('success', 'Kontaktoplysninger er opdateret.');
    }
}
