<?php

namespace Modules\Plans\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Modules\Plans\Models\Payment;
use Modules\Plans\Models\Plan;
use Modules\Plans\Models\UserPlan;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class RazorpayWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $webhookSecret = config('services.razorpay.webhook_secret') ?? env('RAZORPAY_WEBHOOK_SECRET');
        if (!$webhookSecret) {
            return response('Missing RAZORPAY_WEBHOOK_SECRET', 500);
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('X-Razorpay-Signature');

        $key = config('services.razorpay.key') ?? env('RAZORPAY_KEY');
        $secret = config('services.razorpay.secret') ?? env('RAZORPAY_SECRET');

        $api = new Api($key, $secret);

        try {
            $api->utility->verifyWebhookSignature($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationError $e) {
            return response('Invalid signature', 400);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        }

        $data = json_decode($payload, true);

        if (($data['event'] ?? null) === 'order.paid' || ($data['event'] ?? null) === 'payment.captured') {
            $entity = $data['payload']['payment']['entity'] ?? null;
            $orderId = $entity['order_id'] ?? null;

            if (!$orderId) {
                 return response('ok', 200);
            }

            DB::transaction(function () use ($orderId) {
                $payment = Payment::query()
                    ->where('payment_id', $orderId)
                    ->lockForUpdate()
                    ->first();

                if (!$payment) {
                    return;
                }

                if ($payment->status === Payment::STATUS_SUCCESS) {
                    return;
                }

                $payment->update([
                    'status' => Payment::STATUS_SUCCESS,
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

        return response('ok', 200);
    }
}
