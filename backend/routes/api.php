<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\SlotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public auth endpoints
|--------------------------------------------------------------------------
*/
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated endpoints (valid JWT required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    // Account
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Browse the catalog and a provider's free slots (any role).
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('providers/{provider}/slots', [SlotController::class, 'availableForProvider']);

    /*
    |----------------------------------------------------------------------
    | Customer-only
    |----------------------------------------------------------------------
    */
    Route::middleware('role:customer')->group(function () {
        Route::get('bookings', [BookingController::class, 'index']);
        Route::post('bookings', [BookingController::class, 'store']);
        Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel']);
    });

    /*
    |----------------------------------------------------------------------
    | Provider-only
    |----------------------------------------------------------------------
    */
    Route::middleware('role:provider')->prefix('provider')->group(function () {
        // Manage my services
        Route::get('services', [ServiceController::class, 'mine']);
        Route::post('services', [ServiceController::class, 'store']);
        Route::patch('services/{service}', [ServiceController::class, 'update']);
        Route::delete('services/{service}', [ServiceController::class, 'destroy']);

        // Manage my availability slots
        Route::get('slots', [SlotController::class, 'mine']);
        Route::post('slots', [SlotController::class, 'store']);
        Route::delete('slots/{slot}', [SlotController::class, 'destroy']);

        // See bookings made against my services
        Route::get('bookings', [BookingController::class, 'received']);
    });
});
