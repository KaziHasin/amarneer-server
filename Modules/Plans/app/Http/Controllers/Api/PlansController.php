<?php

namespace Modules\Plans\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Plans\Models\Plan;
use Modules\Plans\Services\StripeCheckoutService;
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

    public function checkout(Request $request, Plan $plan, StripeCheckoutService $stripe): CheckoutSessionResource
    {
        $user = $request->user();

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
}

