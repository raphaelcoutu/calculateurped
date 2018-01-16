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
Route::get('/bolus', 'HomeController@bolus');
Route::get('/perfusion', 'HomeController@infusion');
Route::get('/reset', 'HomeController@reset');
Route::get('/pdf', 'HomeController@pdf');
