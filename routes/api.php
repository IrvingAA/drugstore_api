<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [LoginController::class, 'logout']);
    //Users
    Route::get('user/{id}', [LoginController::class, 'getUserInfo']);
    Route::resource('users', UserController::class);
    Route::post('active-user/{id}', [UserController::class, 'activeUser']);
    //Products
    Route::resource('products', ProductController::class);


    Route::get('user-catalogs', [UserController::class, 'userCatalogs']);

});
