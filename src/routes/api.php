<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/convert/sync', [Controllers\Api\v1\ConvertController::class, 'sync']);
    Route::post('/convert/async', [Controllers\Api\v1\ConvertController::class, 'async']);

    // Route::post('/convert/webhook', [Controllers\Api\v1\ConvertController::class, 'webhook']);
});
