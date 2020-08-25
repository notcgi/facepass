<?php

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


Route::get('exchange-rates','\App\Http\Controllers\Controller@updateCurrencyPairs');
Route::get('exchange-rates/history','\App\Http\Controllers\Controller@getHistory');
