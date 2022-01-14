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

// Customer web interface
Route::get('/', function () {
    return view('auth/login');
});
Route::middleware(['auth:sanctum', 'verified'])->get('/hire', [CitiesController::class, 'showMobile'])->name('hire');
Route::middleware(['auth:sanctum', 'verified'])->get('/hire/{id}', [CitiesController::class, 'citymapMobile'])->name('hire-city');
Route::middleware(['auth:sanctum', 'verified'])->get('/history', [LogsController::class, 'showMobile'])->name('history');
Route::middleware(['auth:sanctum', 'verified'])->get('/settings', function () {
    return view('settings');
})->name('settings');
Route::post('settings/update', [CustomersController::class, 'updatepayment']);
Route::post('settings/saldo', [CustomersController::class, 'addsaldo']);
Route::post('history/pay/{id}', [LogsController::class, 'pay']);

// Admin web interface
Route::get('admin/', function () {
    return view('index');
});
Route::get('admin/cities', [CitiesController::class, 'show']);
Route::get('admin/cities/{id}', [CitiesController::class, 'citymap']);
Route::post('admin/cities/addparking', [CitiesController::class, 'addparking']);
Route::post('admin/cities/deleteparking', [CitiesController::class, 'deleteparking']);

Route::get('admin/bikes', [BikesController::class, 'show']);
Route::get('admin/customers', [CustomersController::class, 'show']);
Route::get('admin/customers/{id}', [CustomersController::class, 'customerdetails']);

Route::post('admin/customers/customer/save', [CustomersController::class, 'updatecustomer']);
Route::post('admin/customers/customer/delete', [CustomersController::class, 'deletecustomer']);
Route::post('admin/parking/{id}', [ CitiesController::class, 'parkingdetails']);
Route::post('admin/parking/{id}', [ CitiesController::class, 'parkingdetails']);

Route::get('admin/logs', [LogsController::class, 'show']);


// OAuth routing
Route::get('auth/github', [SocialController::class, 'gitRedirect']);
Route::get('auth/github/callback', [SocialController::class, 'gitLogin']);
Route::get('auth/facebook', [SocialController::class, 'facebookRedirect']);
Route::get('auth/facebook/callback', [SocialController::class, 'facebookLogin']);
