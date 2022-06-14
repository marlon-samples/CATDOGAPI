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

Route::get('/breeds', [App\Http\Controllers\Api\V1\MainController::class, 'breeds'])->name('breeds');
Route::get('/list', [App\Http\Controllers\Api\V1\MainController::class, 'list'])->name('list');
Route::get('/images', [App\Http\Controllers\Api\V1\MainController::class, 'images'])->name('images');
Route::get('/breeds/breed', [App\Http\Controllers\Api\V1\MainController::class, 'breed'])->name('breed');