<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CityController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication & Core Discoveries
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{city:slug}', [CityController::class, 'show']);
Route::get('/offices', [OfficeController::class, 'index']);
Route::get('/offices/{slug}', [OfficeController::class, 'show']);

// Public Favorites (High Compatibility with Frontend raw fetches)
Route::get('/favorites/user/{userId}', [FavoriteController::class, 'getUserFavorites']);
Route::post('/favorites/add', [FavoriteController::class, 'store']);
Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy']);

// Public Bookings & Updates (High Compatibility with Frontend raw fetches)
Route::get('/bookings/all', [BookingController::class, 'allBookings']);
Route::get('/bookings/user/{userId}', [BookingController::class, 'index']);
Route::post('/bookings/create', [BookingController::class, 'store']);
Route::match(['post', 'patch'], '/bookings/{booking}/payment', [BookingController::class, 'uploadPayment']);
Route::patch('/bookings/{booking}/verify', [BookingController::class, 'verifyPayment']);
Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']);

// Protected Sanctum Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Office Management (Admin & Provider)
    Route::post('/offices', [OfficeController::class, 'store']);
    Route::match(['put', 'patch'], '/offices/{office}', [OfficeController::class, 'update']);
    Route::patch('/offices/{office}/fully-booked', [OfficeController::class, 'toggleFullyBooked']);
    Route::delete('/offices/{office}', [OfficeController::class, 'destroy']);

    // City Management (Admin Only)
    Route::post('/cities', [CityController::class, 'store']);

    // Booking Flow (Customer)
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    
    // Chat System
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('/chats/conversation/{userId}', [ChatController::class, 'getConversation']);

    // Standard Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{favorite}', [FavoriteController::class, 'destroy']);
});
