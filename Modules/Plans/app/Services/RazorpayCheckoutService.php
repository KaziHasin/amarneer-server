<?php

namespace Modules\Plans\Services;

use App\Models\User;
use Modules\Plans\Models\Payment;
use Modules\Plans\Models\Plan;
use Razorpay\Api\Api;

class RazorpayCheckoutService
{
    public function createOrder(User $user, Plan $plan): array
    {
        $payment = Payment::query()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'payment_gateway' => 'razorpay',
            'status' => Payment::STATUS_PENDING,
        ]);

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        $api = new Api($key, $secret);

        $orderData = [
            'receipt' => (string) $payment->id,
            'amount' => intval($plan->price * 100),
            'currency' => 'INR',
            'notes' => [
                'plan_id' => (string) $plan->id,
                'user_id' => (string) $user->id,
                'payment_id' => (string) $payment->id,
            ]
        ];

        $razorpayOrder = $api->order->create($orderData);

        $payment->update([
            'payment_id' => $razorpayOrder['id'],
        ]);

        return [
            'payment' => $payment,
            'order' => $razorpayOrder,
        ];
    }
}
