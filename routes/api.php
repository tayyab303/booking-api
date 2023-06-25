<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookingController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['BookingController', 'prefix' => 'booking'], function () {
    
    Route::get('/available-time-slots', [BookingController::class, 'getAvailableTimeSlots']);
   
    // Route::post('/{id}/update', [ImmigrationController::class, 'update']);
});