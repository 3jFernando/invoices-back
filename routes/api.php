<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\InvoicesController;

Route::prefix('v1')->group(function() {

    // Route::get('invoices-test', [InvoicesController::class, 'index']);
    // Route::post('invoices-test', [InvoicesController::class, 'store']);

    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::get('invoices', [InvoicesController::class, 'getAll']);
        Route::get('invoices/{id}', [InvoicesController::class, 'getById']);
        Route::post('invoices', [InvoicesController::class, 'store']);
    });

});
