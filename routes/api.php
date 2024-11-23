<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
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


Route::post('/auth/login', [LoginController::class, 'login']);

// auth
Route::middleware('auth.api')->group(function () {

    // role
    Route::get('/roles', [RoleController::class, 'getRole']);

    // user
    Route::post('/user/create', [UserController::class, 'createUser']);
    Route::get('/user/getall', [UserController::class, 'getAllUser']);
    Route::get('/user/get/{id}', [UserController::class, 'getUser']);
    Route::post('/user/update/{id}', [UserController::class, 'updateUser']);
    Route::delete('/user/delete/{id}', [UserController::class, 'deleteUser']);
});
