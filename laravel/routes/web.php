<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{CitiesController, CustomersController, BikesController, SocialController, LogsController};

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
    return view('index');
});

Route::get('/admin', function() {
    return view('admin');
});

// Admin web interface
Route::get('/cities', [CitiesController::class, 'show']);
Route::get('/cities/{id}', [CitiesController::class, 'citymap']);
Route::post('/cities/addparking', [CitiesController::class, 'addparking']);
Route::post('/cities/deleteparking', [CitiesController::class, 'deleteparking']);

Route::get('/bikes', [BikesController::class, 'show']);
Route::get('/customers', [CustomersController::class, 'show']);
Route::get('/customers/{id}', [CustomersController::class, 'customerdetails']);

Route::post('/customers/customer/save', [CustomersController::class, 'updatecustomer']);
Route::post('/customers/customer/delete', [CustomersController::class, 'deletecustomer']);
Route::post('/parking/{id}', [ CitiesController::class, 'parkingdetails']);
Route::post('/parking/{id}', [ CitiesController::class, 'parkingdetails']);

Route::get('/logs', [LogsController::class, 'show']);

// Customer web interface
Route::get('/mobile', function () {
    return view('auth/login');
});
Route::middleware(['auth:sanctum', 'verified'])->get('/mobile/hire', [CitiesController::class, 'showMobile'])->name('hire');
Route::middleware(['auth:sanctum', 'verified'])->get('/mobile/hire/{id}', [CitiesController::class, 'citymapMobile'])->name('hire-city');
Route::middleware(['auth:sanctum', 'verified'])->get('/mobile/history', [LogsController::class, 'showMobile'])->name('history');
Route::middleware(['auth:sanctum', 'verified'])->get('/mobile/settings', function () {
    return view('settings');
})->name('settings');
Route::post('mobile/settings/update', [CustomersController::class, 'updatepayment']);
Route::post('mobile/history/pay/{id}', [LogsController::class, 'pay']);



// OAuth routing
Route::get('auth/github', [SocialController::class, 'gitRedirect']);
Route::get('auth/github/callback', [SocialController::class, 'gitLogin']);
Route::get('auth/facebook', [SocialController::class, 'facebookRedirect']);
Route::get('auth/facebook/callback', [SocialController::class, 'facebookLogin']);
