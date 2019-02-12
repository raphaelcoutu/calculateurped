<?php

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

Route::get('/', 'HomeController@index');
Route::post('/', 'HomeController@form');

Route::middleware('weight')->group(function () {
    Route::get('/bolus', 'BolusController');
    Route::get('/perfusion', 'InfusionController');
    Route::get('/pdf', 'PdfController');
});

Route::get('/reset', 'HomeController@reset');
