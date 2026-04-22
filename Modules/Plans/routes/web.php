<?php

use Illuminate\Support\Facades\Route;
use Modules\Plans\Http\Controllers\PlansController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('plans', PlansController::class)->names('plans');
});
