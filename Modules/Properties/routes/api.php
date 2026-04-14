<?php

use Illuminate\Support\Facades\Route;
use Modules\Properties\Http\Controllers\Api\PropertiesController;

Route::prefix('v1')->group(function () {
    Route::apiResource('properties', PropertiesController::class);
});
