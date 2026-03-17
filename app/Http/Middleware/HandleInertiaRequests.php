<?php

namespace App\Http\Middleware;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
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
            'studentLearnUrl' => function () use ($request) {
                $student = $request->user()?->student;

                if (! $student) {
                    return null;
                }

                $enrollment = Enrollment::query()
                    ->where('student_id', $student->id)
                    ->where('status', EnrollmentStatus::Completed)
                    ->with(['offer.modules.pages'])
                    ->first();

                if (! $enrollment) {
                    return null;
                }

                $firstModule = $enrollment->offer->modules->first();

                if (! $firstModule) {
                    return null;
                }

                $firstPage = $firstModule->pages->first();

                if (! $firstPage) {
                    return route('student.learn.page', [$enrollment->offer, $firstModule]);
                }

                return route('student.learn.page', [$enrollment->offer, $firstModule, $firstPage]);
            },
        ];
    }
}
