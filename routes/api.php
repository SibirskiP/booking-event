<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Events
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    Route::middleware('role:admin,organizer')->group(function () {
        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);

        Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
        Route::put('/events/{event}/tickets/{ticket}', [TicketController::class, 'update']);
        Route::delete('/events/{event}/tickets/{ticket}', [TicketController::class, 'destroy']);
    });



    // Booking
    Route::middleware('role:customer')->group(function () {
        Route::post('/tickets/{ticket}/bookings', [BookingController::class, 'store'])->middleware('no.double.booking');
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);
        Route::post('/bookings/{booking}/payment', [PaymentController::class, 'store']);
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);

    });

    Route::get('/payments/{payment}', [PaymentController::class, 'show']);
});

Route::get('/events/{event}/tickets', [TicketController::class, 'index']);
