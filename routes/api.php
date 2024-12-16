<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetailsProvider;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::get('/travel_api_fetch', [DetailsProvider::class, 'travel_request_fetch']);
Route::get('/getExpiredRequests', [DetailsProvider::class, 'getExpiredRequests']);
Route::get('/api_travel_details', [DetailsProvider::class, 'api_travel_details']);
Route::Post('/travel_id', [DetailsProvider::class, 'travel_id_fetch']);
