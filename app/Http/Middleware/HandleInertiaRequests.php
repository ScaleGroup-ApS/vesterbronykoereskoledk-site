<?php

namespace App\Http\Middleware;

use App\Enums\OfferType;
use App\Models\Offer;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
                'notifications' => $request->user()?->unreadNotifications()->latest()->take(10)->get(),
                'unread_count' => $request->user()?->unreadNotifications()->count() ?? 0,
            ],
            'branding' => [
                'name' => config('branding.name'),
                'logo' => config('branding.logo_path')
                    ? asset('storage/'.config('branding.logo_path'))
                    : null,
                'colors' => array_filter(config('branding.colors')),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'marketingOffers' => fn (): array => Offer::query()
                ->where('type', OfferType::Primary)
                ->orderBy('name')
                ->get()
                ->map(fn (Offer $offer) => [
                    'id' => $offer->id,
                    'name' => $offer->name,
                    'slug' => $offer->slug,
                    'price' => (string) $offer->price,
                    'type' => $offer->type->value,
                    'theory_lessons' => $offer->theory_lessons,
                    'driving_lessons' => $offer->driving_lessons,
                    'track_required' => $offer->track_required,
                    'slippery_required' => $offer->slippery_required,
                ])
                ->values()
                ->all(),
            'marketingContact' => fn (): array => [
                'phone' => config('marketing.contact.phone'),
                'phone_href' => config('marketing.contact.phone_href'),
                'email' => config('marketing.contact.email'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
