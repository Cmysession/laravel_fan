<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\Web\IndexController::class,'index']);
Route::get('/tong-ji', [\App\Http\Controllers\Web\EchartsController::class,'index']);
Route::get('/sitemap.xml', [\App\Http\Controllers\Web\IndexController::class,'sitemap']);
Route::get('/{list}', [\App\Http\Controllers\Web\IndexController::class,'list']);
Route::get('/{list}.html', [\App\Http\Controllers\Web\IndexController::class,'list']);
Route::get('/{list}/{row}', [\App\Http\Controllers\Web\IndexController::class,'row']);
Route::get('/{list}/{row}.html', [\App\Http\Controllers\Web\IndexController::class,'row']);
Route::get('/{list}/{row}/{show}', [\App\Http\Controllers\Web\IndexController::class,'row']);
Route::get('/{list}/{row}/{show}.html', [\App\Http\Controllers\Web\IndexController::class,'row']);
Route::get('/{list}/{row}/{show}/{id}', [\App\Http\Controllers\Web\IndexController::class,'row']);
Route::get('/{list}/{row}/{show}/{id}.html', [\App\Http\Controllers\Web\IndexController::class,'row']);
