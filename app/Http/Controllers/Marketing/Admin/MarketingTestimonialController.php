<?php

namespace App\Http\Controllers\Marketing\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Marketing\StoreMarketingTestimonialRequest;
use App\Http\Requests\Marketing\UpdateMarketingTestimonialRequest;
use App\Models\MarketingTestimonial;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MarketingTestimonialController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', MarketingTestimonial::class);

        $testimonials = MarketingTestimonial::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('marketing/admin/testimonials/index', [
            'testimonials' => $testimonials,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', MarketingTestimonial::class);

        return Inertia::render('marketing/admin/testimonials/create');
    }

    public function store(StoreMarketingTestimonialRequest $request): RedirectResponse
    {
        $this->authorize('create', MarketingTestimonial::class);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (int) (MarketingTestimonial::query()->max('sort_order') ?? 0) + 1;
        }
        MarketingTestimonial::query()->create($data);

        return redirect()->route('marketing.testimonials.index')
            ->with('success', 'Udtalelsen er oprettet.');
    }

    public function edit(MarketingTestimonial $testimonial): Response
    {
        $this->authorize('update', $testimonial);

        return Inertia::render('marketing/admin/testimonials/edit', [
            'testimonial' => $testimonial,
        ]);
    }

    public function update(UpdateMarketingTestimonialRequest $request, MarketingTestimonial $testimonial): RedirectResponse
    {
        $this->authorize('update', $testimonial);

        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $testimonial->update($data);

        return redirect()->route('marketing.testimonials.index')
            ->with('success', 'Udtalelsen er opdateret.');
    }

    public function destroy(MarketingTestimonial $testimonial): RedirectResponse
    {
        $this->authorize('delete', $testimonial);

        $testimonial->delete();

        return redirect()->route('marketing.testimonials.index')
            ->with('success', 'Udtalelsen er slettet.');
    }
}
