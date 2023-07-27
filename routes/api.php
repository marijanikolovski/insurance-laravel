<?php

use App\Http\Controllers\AgesController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\CoveragesController;
use App\Http\Controllers\DiscountsController;
use App\Http\Controllers\InsuranceController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(CitiesController::class)->group(
    function () {
        Route::get('/cities', 'index');
        Route::post('/cities', 'store');
    }
);

Route::controller(AgesController::class)->group(
    function () {
        Route::get('/age', 'index');
        Route::post('/age', 'store');
    }
);

Route::controller(DiscountsController::class)->group(
    function () {
        Route::get('/discounts', 'index');
        Route::post('/discounts', 'store');
    }
);

Route::controller(CoveragesController::class)->group(
    function () {
        Route::get('/coverages', 'index');
        Route::post('/coverages', 'store');
    }
);


Route::controller(InsuranceController::class)->group(
    function () {
        Route::post('/total', 'calculatePrice');
    }
);