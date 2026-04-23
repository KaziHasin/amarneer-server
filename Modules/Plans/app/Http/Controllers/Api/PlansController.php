<?php

namespace Modules\Plans\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Plans\Models\Plan;
use Modules\Plans\Services\StripeCheckoutService;
use Modules\Plans\Services\RazorpayCheckoutService;
use Modules\Plans\Transformers\CheckoutSessionResource;
use Modules\Plans\Transformers\PlanResource;

class PlansController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $plans = Plan::query()
            ->orderBy('price')
            ->get(['id', 'name', 'price', 'duration_days', 'contact_limit']);

        return PlanResource::collection($plans);
    }

    public function checkout(Request $request, Plan $plan, StripeCheckoutService $stripe, RazorpayCheckoutService $razorpay): CheckoutSessionResource
    {
        $user = $request->user();
        $gateway = $request->input('gateway', 'razorpay');

        if ($gateway === 'stripe') {
            if (!config('plans.stripe.secret')) {
                abort(500, 'Stripe is not configured (missing STRIPE_SECRET).');
            }

            $result = $stripe->createCheckoutSession($user, $plan);

            return new CheckoutSessionResource([
                'checkout_url' => $result['session']->url,
                'session_id' => $result['session']->id,
                'payment_id' => $result['payment']->id,
            ]);
        }

        if ($gateway === 'razorpay') {
            $key = config('services.razorpay.key');
            $secret = config('services.razorpay.secret');

            if (!$key || !$secret) {
                abort(500, 'Razorpay is not configured (missing RAZORPAY_KEY or RAZORPAY_SECRET).');
            }

            $result = $razorpay->createOrder($user, $plan);

            return new CheckoutSessionResource([
                'order_id' => $result['order']->id,
                'payment_id' => $result['payment']->id,
                'razorpay_key' => $key,
            ]);
        }

        abort(400, 'Invalid payment gateway.');
    }
}

