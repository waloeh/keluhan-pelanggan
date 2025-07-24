<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KeluhanPelangganController;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::resource('keluhan', KeluhanPelangganController::class);
    Route::get('/export/{format}', [KeluhanPelangganController::class, 'exportExcel']);
    Route::get('/export-pdf', [KeluhanPelangganController::class, 'exportPdf']);
    Route::get('/export-txt', [KeluhanPelangganController::class, 'exportTxt']);
    Route::get('/summary', [HomeController::class, 'summary']);
});

Route::get('/', function() {
    return redirect('/home');
});
Route::get('/{any}', [App\Http\Controllers\HomeController::class, 'index'])
    ->where('any', '.*')
    ->name('home');
