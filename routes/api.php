<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Routes
Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{city:slug}', [CityController::class, 'show']);
Route::get('/offices', [OfficeController::class, 'index']);
Route::get('/offices/{slug}', [OfficeController::class, 'show']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Office Management (Admin & Provider)
    Route::post('/offices', [OfficeController::class, 'store']);
    Route::match(['put', 'patch'], '/offices/{office}', [OfficeController::class, 'update']);
    Route::delete('/offices/{office}', [OfficeController::class, 'destroy']);

    // City Management (Admin Only)
    Route::post('/cities', [CityController::class, 'store']);

    // Booking Flow (Customer)
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'uploadPayment']);
    
    // Booking Verification (Admin & Provider)
    Route::patch('/bookings/{booking}/verify', [BookingController::class, 'verifyPayment']);

    // Chat System
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/conversation/{userId}', [ChatController::class, 'getConversation']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy']);
});
