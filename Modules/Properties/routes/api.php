<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\Api\CategoriesController;
use Modules\Properties\Http\Controllers\Api\PropertiesController;
use Modules\Properties\Http\Controllers\Api\AmenitiesController;

Route::prefix('v1')->group(function () {
    Route::get('properties/stats', [PropertiesController::class, 'getStats']);
    Route::get('properties/max-price', [PropertiesController::class, 'getMaxPrice']);
    Route::apiResource('properties', PropertiesController::class);
    Route::get('categories', [CategoriesController::class, 'index']);
    Route::get('amenities', [AmenitiesController::class, 'index']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('properties/{property}/unlock-contact', [PropertiesController::class, 'unlockContact']);
    });
});
