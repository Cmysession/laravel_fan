<?php

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
Route::get('/get_all_host', [\App\Http\Controllers\Web\EchartsController::class, 'get_all_host']);
Route::get('/get_charts_data', [\App\Http\Controllers\Web\EchartsController::class, 'get_charts_data']);
Route::get('/get_day_charts', [\App\Http\Controllers\Web\EchartsController::class, 'get_day_charts']);
Route::get('/get_table_list', [\App\Http\Controllers\Web\EchartsController::class, 'get_table_list']);
