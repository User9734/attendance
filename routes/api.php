<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClockController;
use App\Http\Controllers\JustificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
  
    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
    });
});

Route::apiResource('/users', UserController::class);
Route::get('/salaries', [UserController::class, 'getSalaries']);

Route::apiResource('/clocks', ClockController::class);
Route::get('/byday', [ClockController::class, 'indexbyday']);
Route::get('/perfs', [ClockController::class, 'getPerformances']);
Route::post('/recaps', [ClockController::class, 'getRecapitulatifs']);

Route::apiResource('/profiles', ProfileController::class)->middleware('auth:api');

Route::apiResource('/justifs', JustificationController::class);
Route::get('/unvalidated', [JustificationController::class, 'getUnvalidated']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
