<?php

use App\Http\Controllers\Api\PlatformFeatureController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('platform')->group(function () {
    Route::get('payments', [PlatformFeatureController::class, 'payments']);
    Route::post('payments', [PlatformFeatureController::class, 'storePayment']);
    Route::get('invoices', [PlatformFeatureController::class, 'invoices']);
    Route::get('subscriptions', [PlatformFeatureController::class, 'subscriptions']);
    Route::get('employees', [PlatformFeatureController::class, 'employees']);
    Route::get('channels', [PlatformFeatureController::class, 'channels']);
    Route::get('channel-packages', [PlatformFeatureController::class, 'channelPackages']);
});
