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
    Route::post('register', [AuthController::class, 'register'])->name('auth.register')->middleware('throttle:100,1');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login')->middleware('throttle:100,1');
    Route::get('trips/available', [TripController::class, 'available']);

    // Protected (Sanctum)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

         /**
         * Управление поездками
         */
        Route::prefix('trips')->group(function () {
            Route::post('/', [TripController::class, 'store'])->middleware('role:passenger');
            Route::get('/', [TripController::class, 'index']);
            Route::get('{id}', [TripController::class, 'show']);
            Route::put('{id}', [TripController::class, 'update'])->middleware('role:passenger');
            Route::delete('{id}', [TripController::class, 'destroy'])->middleware('role:passenger');

            // действия водителя
            Route::post('{id}/accept', [TripController::class, 'accept'])->middleware('role:driver');
            Route::post('{id}/reject', [TripController::class, 'reject'])->middleware('role:driver');
            Route::post('{id}/complete', [TripController::class, 'complete'])->middleware('role:driver');

            // тестовый endpoint
         Route::middleware(['auth:sanctum'])->post('trips/{id}/test-broadcast', [TripController::class, 'testBroadcast']);
        });

         /**
         * Машины (только для водителей)
         */
        Route::middleware(['auth:sanctum', 'role:driver'])->post('cars', [CarController::class, 'store']);
        Route::middleware('role:driver')->prefix('cars')->group(function () {
            Route::post('/', [CarController::class, 'store']);
            Route::get('/', [CarController::class, 'index']);
            Route::delete('{id}', [CarController::class, 'destroy']);
        });

        /**
         * Отзывы
         */
        Route::prefix('reviews')->group(function () {
            Route::post('{driverId}', [ReviewController::class, 'store'])->middleware('role:passenger');
            Route::get('{driverId}', [ReviewController::class, 'index']);
        });
    });
});
