<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;

//Route::post('/customer/callback', [SaleController::class, 'paymentCallback'])->name('customer.callback')->withoutMiddleware(['auth:api']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customer/balance', [CustomerController::class, 'getBalance']);
    Route::get('/customer/notifications', [CustomerController::class, 'getNotifications']);	
});