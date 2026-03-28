<?php

namespace App\Http\Controllers\Marketing\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreMarketingValueBlockRequest;
use App\Http\Requests\Marketing\UpdateMarketingValueBlockRequest;
use App\Models\MarketingValueBlock;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MarketingValueBlockController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', MarketingValueBlock::class);

        $blocks = MarketingValueBlock::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('marketing/admin/value-blocks/index', [
            'blocks' => $blocks,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', MarketingValueBlock::class);

        return Inertia::render('marketing/admin/value-blocks/create');
    }

    public function store(StoreMarketingValueBlockRequest $request): RedirectResponse
    {
        $this->authorize('create', MarketingValueBlock::class);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (int) (MarketingValueBlock::query()->max('sort_order') ?? 0) + 1;
        }
        MarketingValueBlock::query()->create($data);

        return redirect()->route('marketing.value-blocks.index')
            ->with('success', 'Blokken er oprettet.');
    }

    public function edit(MarketingValueBlock $value_block): Response
    {
        $this->authorize('update', $value_block);

        return Inertia::render('marketing/admin/value-blocks/edit', [
            'block' => $value_block,
        ]);
    }

    public function update(UpdateMarketingValueBlockRequest $request, MarketingValueBlock $value_block): RedirectResponse
    {
        $this->authorize('update', $value_block);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $value_block->update($data);

        return redirect()->route('marketing.value-blocks.index')
            ->with('success', 'Blokken er opdateret.');
    }

    public function destroy(MarketingValueBlock $value_block): RedirectResponse
    {
        $this->authorize('delete', $value_block);

        $value_block->delete();

        return redirect()->route('marketing.value-blocks.index')
            ->with('success', 'Blokken er slettet.');
    }
}
