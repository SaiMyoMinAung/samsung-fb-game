<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::get('abc/{id}', function (Request $request) {
    dd(storage_path() . '/user_profiles/' . $request->id . '.jpg');
});
Route::controller(FacebookController::class)->group(function () {
    Route::get('/', 'facebookLogin');
    Route::get('samsung-tv', 'samsungTv')->name('samsung-tv');
    Route::get('try-samsung-tv', 'trySamsungTv')->name('try-samsung-tv');
    Route::get('delete-user-data', 'deleteUserData')->name('delete-user-data');
    Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
    Route::get('auth/facebook/callback', 'handleFacebookCallback');
    Route::get('privacy-policy', 'policy');
    Route::get('terms-of-service', 'termsOfService');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/export', [App\Http\Controllers\HomeController::class, 'exportUsers'])->name('export-users');
