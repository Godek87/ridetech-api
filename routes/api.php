<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TripController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\ReviewController;


// Все маршруты под префиксом /api/v1
Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public
    Route::post('register', [AuthController::class, 'register'])->name('auth.register')->middleware('throttle:5,1');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login')->middleware('throttle:10,1');
    Route::get('trips/available', [TripController::class, 'available']);

    // Protected (Sanctum)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::post('trips', [TripController::class, 'store'])->middleware('role:passenger');
        Route::get('trips', [TripController::class, 'index']);
        Route::get('trips/{id}', [TripController::class, 'show']);
        Route::put('trips/{id}', [TripController::class, 'update'])->middleware('role:passenger');
        Route::delete('trips/{id}', [TripController::class, 'destroy'])->middleware('role:passenger');
        Route::post('trips/{id}/accept', [TripController::class, 'accept'])->middleware('role:driver');
        Route::post('trips/{id}/reject', [TripController::class, 'reject'])->middleware('role:driver');
        Route::post('trips/{id}/complete', [TripController::class, 'complete'])->middleware('role:driver');
        Route::post('trips/{id}/test-broadcast', [TripController::class, 'testBroadcast']);

        // Cars (driver only)
        Route::middleware('role:driver')->group(function () {
            Route::post('cars', [CarController::class, 'store']);
            Route::get('cars', [CarController::class, 'index']);
            Route::delete('cars/{id}', [CarController::class, 'destroy']);
        });

        // Reviews
        Route::get('reviews/{driverId}', [ReviewController::class, 'index']);
    });
});
