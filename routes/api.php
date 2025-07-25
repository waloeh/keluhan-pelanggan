<?php

use App\Http\Controllers\Api\KeluhanController;
use App\Http\Controllers\Auth\LoginController;
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

Route::post('/login', [KeluhanController::class, 'apiLogin']);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/status-keluhan/{id}', [KeluhanController::class, 'updateStatus']);
    Route::delete('/status-keluhan/{id}', [KeluhanController::class, 'deleteStatus']);
    Route::post('/logout', [KeluhanController::class, 'apiLogout']);
});
