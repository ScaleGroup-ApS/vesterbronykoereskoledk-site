<?php

namespace App\Http\Controllers\Enrollment;

use App\Actions\Enrollment\CompleteStripeEnrollment;
use App\Actions\Enrollment\CreateStripeCheckoutSession;
use App\Actions\Enrollment\InitiateEnrollment;
use App\Enums\EnrollmentPaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class EnrollmentController extends Controller
{
    public function show(Offer $offer): never
    {
        abort(501, 'Enrollment page not yet implemented in Blade/Livewire.');
    }

    public function store(
        StoreEnrollmentRequest $request,
        Offer $offer,
        InitiateEnrollment $initiateEnrollment,
        CreateStripeCheckoutSession $createStripeCheckoutSession,
    ): RedirectResponse|SymfonyResponse {
        $validated = $request->validated();

        [$student, $enrollment] = $initiateEnrollment->handle($validated, $offer);

        Auth::login($student->user);

        if (EnrollmentPaymentMethod::from($validated['payment_method']) === EnrollmentPaymentMethod::Stripe) {
            $checkoutUrl = $createStripeCheckoutSession->handle($enrollment);

            return redirect()->away($checkoutUrl);
        }

        return redirect('/app');
    }

    public function stripeReturn(Request $request, CompleteStripeEnrollment $completeStripeEnrollment): RedirectResponse
    {
        $sessionId = $request->string('session_id')->value();

        $completeStripeEnrollment->handle($sessionId);

        return redirect('/app');
    }
}
