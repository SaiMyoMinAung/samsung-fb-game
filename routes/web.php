<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacebookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(FacebookController::class)->group(function () {
    Route::get('/', 'faceboolLogin');
    Route::get('samsung-tv', 'samsungTv')->name('samsung-tv');
    Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
    Route::get('auth/facebook/callback', 'handleFacebookCallback');
});
