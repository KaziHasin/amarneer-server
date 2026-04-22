<?php

use Illuminate\Support\Facades\Route;
use Modules\Plans\Http\Controllers\Api\PlansController;
use Modules\Plans\Http\Controllers\Api\StripeWebhookController;

Route::prefix('v1')->group(function () {
    Route::get('plans', [PlansController::class, 'index']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('plans/{plan}/checkout', [PlansController::class, 'checkout']);
    });
});

// Stripe webhooks must be publicly reachable. Protect via signature verification.
Route::post('plans/stripe/webhook', [StripeWebhookController::class, 'handle']);
