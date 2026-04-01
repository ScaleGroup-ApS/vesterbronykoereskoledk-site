<?php

namespace App\Http\Controllers\Offers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Offers\StoreOfferModuleRequest;
use App\Http\Requests\Offers\UpdateOfferModuleRequest;
use App\Models\Offer;
use App\Models\OfferModule;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OfferModuleController extends Controller
{
    public function index(Offer $offer): Response
    {
        $this->authorize('viewAny', OfferModule::class);

        $modules = $offer->modules()->with('pages')->get();

        return Inertia::render('offers/modules/index', [
            'offer' => $offer,
            'modules' => $modules,
        ]);
    }

    public function store(StoreOfferModuleRequest $request, Offer $offer): RedirectResponse
    {
        $maxOrder = $offer->modules()->max('sort_order') ?? -1;

        $offer->modules()->create([
            'title' => $request->validated('title'),
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('offers.modules.index', $offer)
            ->with('success', 'Modul oprettet.');
    }

    public function edit(Offer $offer, OfferModule $module): Response
    {
        $this->authorize('update', $module);

        return Inertia::render('offers/modules/edit', [
            'offer' => $offer,
            'module' => $module,
        ]);
    }

    public function update(UpdateOfferModuleRequest $request, Offer $offer, OfferModule $module): RedirectResponse
    {
        $module->update($request->validated());

        return redirect()->route('offers.modules.index', $offer)
            ->with('success', 'Modul opdateret.');
    }

    public function destroy(Offer $offer, OfferModule $module): RedirectResponse
    {
        $this->authorize('delete', $module);

        $module->delete();

        return redirect()->route('offers.modules.index', $offer)
            ->with('success', 'Modul slettet.');
    }

    public function moveUp(Offer $offer, OfferModule $module): RedirectResponse
    {
        $this->authorize('update', $module);

        $previous = $offer->modules()
            ->where('sort_order', '<', $module->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previous) {
            [$module->sort_order, $previous->sort_order] = [$previous->sort_order, $module->sort_order];
            $module->save();
            $previous->save();
        }

        return redirect()->route('offers.modules.index', $offer);
    }

    public function moveDown(Offer $offer, OfferModule $module): RedirectResponse
    {
        $this->authorize('update', $module);

        $next = $offer->modules()
            ->where('sort_order', '>', $module->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            [$module->sort_order, $next->sort_order] = [$next->sort_order, $module->sort_order];
            $module->save();
            $next->save();
        }

        return redirect()->route('offers.modules.index', $offer);
    }
}
