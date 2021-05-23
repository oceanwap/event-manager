<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/events', [EventController::class, 'index']);
Route::post('/event/booking', [EventController::class, 'postBooking']);
Route::get('/event/{id}/booking', [EventController::class, 'getBookings']);
Route::get('/event/bookings', [BookingController::class, 'index']); 