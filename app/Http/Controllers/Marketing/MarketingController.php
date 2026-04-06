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
use Illuminate\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        $heroCourse = Course::query()
            ->with('offer')
            ->upcoming()
            ->where('featured_on_home', true)
            ->orderBy('start_at')
            ->first()
            ?? Course::query()
                ->with('offer')
                ->upcoming()
                ->orderBy('start_at')
                ->first();

        $nextOffer = $heroCourse?->offer;
        $offerForEnrollment = ($nextOffer && $nextOffer->type === OfferType::Primary)
            ? $nextOffer
            : Offer::query()->primary()->orderBy('name')->first();

        return view('welcome', [
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
            'nextHoldStartAt' => $heroCourse?->start_at?->toIso8601String(),
            'heroHoldSpotsRemaining' => $heroCourse?->public_spots_remaining,
            'tilmeldHoldstartOfferSlug' => $offerForEnrollment?->slug,
            'marketingOffers' => Offer::query()
                ->where('type', OfferType::Primary)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function features(): View
    {
        return view('marketing.fordele');
    }

    public function packages(): View
    {
        return view('marketing.pakker', [
            'offers' => Offer::query()->primary()->orderBy('name')->get(),
            'addons' => Offer::query()->addon()->orderBy('name')->get(),
        ]);
    }

    public function packageShow(Offer $offer): View
    {
        if ($offer->type !== OfferType::Primary) {
            abort(404);
        }

        return view('marketing.pakke-show', [
            'offer' => $offer,
        ]);
    }

    public function faq(): View
    {
        return view('marketing.faq', [
            'items' => config('marketing.faq', []),
        ]);
    }

    public function instructors(): View
    {
        return view('marketing.vores-korelaerere', [
            'teams' => Team::query()->orderBy('name')->get(['id', 'name', 'description']),
        ]);
    }

    public function forStudents(string $slug): View
    {
        $pages = config('marketing.til_elever', []);
        if (! isset($pages[$slug])) {
            abort(404);
        }

        $page = $pages[$slug];

        return view('marketing.for-students', [
            'slug' => $slug,
            'metaTitle' => $page['metaTitle'],
            'heading' => $page['heading'],
            'lead' => $page['lead'],
            'sections' => $page['sections'],
        ]);
    }

    public function about(): View
    {
        return view('marketing.om-os');
    }

    public function contact(): View
    {
        return view('marketing.kontakt', [
            'offers' => Offer::query()
                ->primary()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'price']),
            'holdStartOptions' => config('marketing.hold_start_options', []),
            'phone' => config('marketing.contact.phone'),
            'phone_href' => config('marketing.contact.phone_href'),
            'email' => config('marketing.contact.email'),
            'success' => session('success'),
        ]);
    }

    public function terms(): View
    {
        return view('marketing.handelsbetingelser');
    }

    public function privacy(): View
    {
        return view('marketing.privatlivspolitik');
    }

    public function cookies(): View
    {
        return view('marketing.cookiepolitik');
    }
}
