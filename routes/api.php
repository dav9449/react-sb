<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DTR;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/upload-and-parse', [DTR::class, 'uploadRoasterFromFile']);
Route::get('/get-all-events-by-date', [DTR::class, 'getAllEventsByDate']);
Route::get('/get-all-flights-for-next-week', [DTR::class, 'getAllFlightsForNextWeek']);
Route::get('/set-all-flights-events-by-given-location', [DTR::class, 'getAllFlightsEventsByGivenLocation']);
Route::post('/set-all-standby-events-for-next-week', [DTR::class, 'setAllStandbyEventsForNextWeek']);