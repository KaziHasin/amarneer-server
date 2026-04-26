<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\Api\CategoriesController;
use Modules\Properties\Http\Controllers\Api\PropertiesController;
use Modules\Properties\Http\Controllers\Api\AmenitiesController;

Route::prefix('v1')->group(function () {
    Route::apiResource('properties', PropertiesController::class);
    Route::get('categories', [CategoriesController::class, 'index']);
    Route::get('amenities', [AmenitiesController::class, 'index']);
});
