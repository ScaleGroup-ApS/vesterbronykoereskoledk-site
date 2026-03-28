<?php

namespace App\Http\Controllers\Marketing;

use App\Enums\OfferType;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\MarketingHomeCopy;
use App\Models\MarketingTestimonial;
use App\Models\MarketingValueBlock;
use App\Models\Offer;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class MarketingController extends Controller
{
    public function home(): Response
    {
        $nextCourse = Course::query()
            ->with('offer')
            ->upcoming()
            ->orderBy('start_at')
            ->first();

        $nextOffer = $nextCourse?->offer;
        $offerForEnrollment = ($nextOffer && $nextOffer->type === OfferType::Primary)
            ? $nextOffer
            : Offer::query()->primary()->orderBy('name')->first();

        return Inertia::render('welcome', [
            'homeCopy' => MarketingHomeCopy::forHome(),
            'valueBlocks' => MarketingValueBlock::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'testimonials' => MarketingTestimonial::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
            'nextHoldStartAt' => $nextCourse?->start_at?->toIso8601String(),
            'tilmeldHoldstartOfferSlug' => $offerForEnrollment?->slug,
        ]);
    }

    public function features(): Response
    {
        return Inertia::render('marketing/fordele');
    }

    public function packages(): Response
    {
        return Inertia::render('marketing/pakker', [
            'offers' => Offer::query()->primary()->orderBy('name')->get(),
            'addons' => Offer::query()->addon()->orderBy('name')->get(),
        ]);
    }

    public function packageShow(Offer $offer): Response
    {
        if ($offer->type !== OfferType::Primary) {
            abort(404);
        }

        return Inertia::render('marketing/pakke-show', [
            'offer' => $offer,
        ]);
    }

    public function faq(): Response
    {
        return Inertia::render('marketing/faq', [
            'items' => config('marketing.faq', []),
        ]);
    }

    public function instructors(): Response
    {
        return Inertia::render('marketing/vores-korelaerere', [
            'teams' => Team::query()->orderBy('name')->get(['id', 'name', 'description']),
        ]);
    }

    public function tilElever(string $slug): Response
    {
        $pages = config('marketing.til_elever', []);
        if (! isset($pages[$slug])) {
            abort(404);
        }

        $page = $pages[$slug];

        return Inertia::render('marketing/til-elever-side', [
            'slug' => $slug,
            'metaTitle' => $page['metaTitle'],
            'heading' => $page['heading'],
            'lead' => $page['lead'],
            'sections' => $page['sections'],
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('marketing/om-os');
    }

    public function contact(): Response
    {
        return Inertia::render('marketing/kontakt', [
            'offers' => Offer::query()
                ->primary()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'price']),
            'holdStartOptions' => config('marketing.hold_start_options', []),
        ]);
    }

    public function terms(): Response
    {
        return Inertia::render('marketing/handelsbetingelser');
    }

    public function privacy(): Response
    {
        return Inertia::render('marketing/privatlivspolitik');
    }

    public function cookies(): Response
    {
        return Inertia::render('marketing/cookiepolitik');
    }
}
