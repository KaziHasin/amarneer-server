<?php

namespace Modules\Plans\Services;

use App\Models\User;
use Modules\Plans\Models\Payment;
use Modules\Plans\Models\Plan;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeCheckoutService
{
    public function createCheckoutSession(User $user, Plan $plan): array
    {
        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_gateway' => 'stripe',
            'status' => Payment::STATUS_PENDING,
        ]);

        $stripe = new StripeClient(config('plans.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'customer_email' => $user->email,
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => config('plans.stripe.currency', 'inr'),
                        'unit_amount' => $plan->price,
                        'product_data' => [
                            'name' => $plan->name,
                        ],
                    ],
                ],
            ],
            'success_url' => config('plans.stripe.success_url') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('plans.stripe.cancel_url'),
            'metadata' => [
                'payment_id' => (string) $payment->id,
                'user_id' => (string) $user->id,
                'plan_id' => (string) $plan->id,
            ],
        ]);

        $payment->update([
            'payment_id' => $session->id,
        ]);

        return [
            'payment' => $payment,
            'session' => $session,
        ];
    }
}

