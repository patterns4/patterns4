<?php

use Illuminate\Support\Facades\Route;
USE App\Http\Controllers\{CitiesController, CustomersController, BikesController};

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

Route::get('/cities', [CitiesController::class, 'show']);
Route::get('/cities/{id}', [CitiesController::class, 'citymap']);
Route::post('/cities/addparking', [CitiesController::class, 'addparking']);

Route::get('/bikes', [BikesController::class, 'show']);
Route::get('/customers', [CustomersController::class, 'show']);
Route::get('/customers/{id}', [CustomersController::class, 'customerdetails']);

Route::post('/customers/customer/save', [CustomersController::class, 'updatecustomer']);
Route::post('/customers/customer/delete', [CustomersController::class, 'deletecustomer']);
Route::post('/parking/{id}', [ CitiesController::class, 'parkingdetails']);
Route::post('/parking/{id}', [ CitiesController::class, 'parkingdetails']);