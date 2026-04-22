<?php

namespace Modules\Plans\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Plans\Models\Payment;
use Modules\Plans\Models\Plan;
use Modules\Plans\Models\UserPlan;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $secret = config('plans.stripe.webhook_secret');
        if (!$secret) {
            return response('Missing STRIPE_WEBHOOK_SECRET', 500);
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException) {
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException) {
            return response('Invalid payload', 400);
        }

        // We only need checkout.session events for one-time payments.
        if (($event->type ?? null) === 'checkout.session.completed') {
            $session = $event->data->object;
            $metadata = (array) ($session->metadata ?? []);
            $paymentRowId = $metadata['payment_id'] ?? null;

            DB::transaction(function () use ($paymentRowId, $session) {
                if ($paymentRowId) {
                    $payment = Payment::query()->lockForUpdate()->find($paymentRowId);
                } else {
                    $payment = Payment::query()
                        ->where('payment_id', $session->id)
                        ->lockForUpdate()
                        ->first();
                }

                if (!$payment) {
                    return;
                }

                if ($payment->status === Payment::STATUS_SUCCESS) {
                    return;
                }

                $payment->update([
                    'status' => Payment::STATUS_SUCCESS,
                    'payment_id' => $session->id,
                ]);

                $plan = Plan::query()->find($payment->plan_id);
                if (!$plan) {
                    return;
                }

                UserPlan::query()->create([
                    'user_id' => $payment->user_id,
                    'plan_id' => $payment->plan_id,
                    'starts_at' => now(),
                    'expires_at' => now()->addDays($plan->duration_days),
                    'contacts_used' => 0,
                ]);
            });
        }

        if (($event->type ?? null) === 'checkout.session.expired') {
            $session = $event->data->object;

            Payment::query()
                ->where('payment_id', $session->id)
                ->where('status', Payment::STATUS_PENDING)
                ->update(['status' => Payment::STATUS_FAILED]);
        }

        return response('ok', 200);
    }
}

