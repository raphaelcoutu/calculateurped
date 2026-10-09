<?php

use App\Http\Controllers\Admin\BolusController as AdminBolusController;
use App\Http\Controllers\Admin\OrganizationAdministratorController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\BolusController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfusionController;
use App\Http\Controllers\OrganizationProfileController;
use App\Http\Controllers\PdfController;
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
    Route::get('/infusion', InfusionController::class);
    Route::get('/pdf', PdfController::class);
});

Route::get('/reset', [HomeController::class, 'reset']);

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('boluses/catalog', [AdminBolusController::class, 'catalog'])->name('boluses.catalog');
    Route::get('boluses', [AdminBolusController::class, 'index'])->name('boluses.index');
    Route::get('boluses/create', [AdminBolusController::class, 'create'])->name('boluses.create');
    Route::post('boluses', [AdminBolusController::class, 'store'])->name('boluses.store');
    Route::get('boluses/{bolus}/edit', [AdminBolusController::class, 'edit'])->name('boluses.edit');
    Route::put('boluses/{bolus}', [AdminBolusController::class, 'update'])->name('boluses.update');
    Route::post('boluses/{bolus}/revise', [AdminBolusController::class, 'revise'])->name('boluses.revise');
    Route::post('boluses/{bolus}/publish', [AdminBolusController::class, 'publish'])->name('boluses.publish');
    Route::post('boluses/{bolus}/copy', [AdminBolusController::class, 'copy'])->name('boluses.copy');
    Route::delete('boluses/{bolus}', [AdminBolusController::class, 'destroy'])->name('boluses.destroy');
    Route::post('boluses/{bolus}/restore', [AdminBolusController::class, 'restore'])->name('boluses.restore');
    Route::get('boluses/{bolus}', [AdminBolusController::class, 'show'])->withTrashed()->name('boluses.show');

    Route::resource('organizations', OrganizationController::class)->only(['index', 'create', 'store', 'show', 'edit']);
    Route::put('organizations/{organization}', [OrganizationProfileController::class, 'update'])
        ->name('organizations.update');
    Route::post('organizations/{organization}/administrators', [OrganizationAdministratorController::class, 'store'])
        ->name('organizations.administrators.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/organization/profile', [OrganizationProfileController::class, 'edit'])->name('organization.profile.edit');
    Route::put('/organization/profile', [OrganizationProfileController::class, 'update'])->name('organization.profile.update');
});
