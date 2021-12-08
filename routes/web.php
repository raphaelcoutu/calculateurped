<?php

use App\Http\Controllers\BolusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfusionController;
use App\Http\Controllers\PdfController;
use App\Models\Bolus;
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

Route::get('/', [HomeController::class, 'index']);
Route::post('/', [HomeController::class, 'form']);

Route::middleware('weight')->group(function () {
    Route::get('/bolus', BolusController::class);
    Route::get('/perfusion', InfusionController::class);
    Route::get('/pdf', PdfController::class);
});

Route::get('/reset', [HomeController::class, 'reset']);
