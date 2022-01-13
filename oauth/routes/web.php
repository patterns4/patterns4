<?php

use Illuminate\Support\Facades\Route;
USE App\Http\Controllers\{GitHubController, FacebookController, CitiesController};

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
    return view('auth/login');
});

Route::get('/city/{id}', [CitiesController::class, 'citymap']);

Route::get('auth/github', [GitHubController::class, 'gitRedirect']);
Route::get('auth/github/callback', [GitHubController::class, 'gitCallback']);

Route::get('auth/facebook', [FacebookController::class, 'facebookRedirect']);
Route::get('auth/facebook/callback', [FacebookController::class, 'loginWithFacebook']);

Route::middleware(['auth:sanctum', 'verified'])->get('/hire', [CitiesController::class, 'show'])->name('hire');
Route::middleware(['auth:sanctum', 'verified'])->get('/hire/{id}', [CitiesController::class, 'citymap'])->name('hire-city');

Route::middleware(['auth:sanctum', 'verified'])->get('/history', function () {
    return view('history');
})->name('history');

Route::middleware(['auth:sanctum', 'verified'])->get('/settings', function () {
    return view('settings');
})->name('settings');
