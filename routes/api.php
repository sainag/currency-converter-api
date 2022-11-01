<?php

use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\AuthController;
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

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::middleware(['throttle:currencyapi'])->controller(CurrencyController::class)->prefix('currency')->group(function () {
        Route::get('/', 'getCurrencies');
        Route::get('/convert', 'convertCurrencies');
        Route::get('/report', 'generateReport');
        Route::get('/{userId}/report', 'getReports');
        Route::get('/{userId}/report/{reportId}', 'getReportId');
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

//public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



