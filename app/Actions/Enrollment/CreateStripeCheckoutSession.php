<?php

namespace App\Actions\Enrollment;

use App\Models\EnrollmentRequest;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CreateStripeCheckoutSession
{
    public function handle(EnrollmentRequest $enrollmentRequest): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $enrollmentRequest->load('offer');

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'dkk',
                        'unit_amount' => (int) ($enrollmentRequest->offer->price * 100),
                        'product_data' => [
                            'name' => $enrollmentRequest->offer->name,
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('enrollment.stripe-return').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('enrollment.show', $enrollmentRequest->offer_id),
            'metadata' => [
                'enrollment_request_id' => $enrollmentRequest->id,
            ],
        ]);

        $enrollmentRequest->update(['stripe_session_id' => $session->id]);

        return $session->url;
    }
}
