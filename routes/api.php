<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UsersController;

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
    Route::get('user/{id}', [LoginController::class, 'getUserInfo']);
    Route::resource('users',UsersController::class);
    Route::post('active-user/{id}', [UsersController::class, 'activeUser']);

    Route::get('user-catalogs', [UsersController::class,'userCatalogs']);

});
