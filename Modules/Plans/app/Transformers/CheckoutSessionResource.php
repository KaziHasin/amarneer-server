<?php

namespace Modules\Plans\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'checkout_url' => $this['checkout_url'] ?? null,
            'session_id' => $this['session_id'] ?? null,
            'payment_id' => $this['payment_id'] ?? null,
            'order_id' => $this['order_id'] ?? null,
            'razorpay_key' => $this['razorpay_key'] ?? null,
        ];
    }
}

