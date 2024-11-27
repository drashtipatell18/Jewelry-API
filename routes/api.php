<?php

use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\MainCategoryController;
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
Route::post('/user/updateprofile/{id}', [UserController::class, 'updateProfile']);
Route::post('password/email', [UserController::class, 'sendResetLinkEmail']);
Route::post('password/reset/{token}', [UserController::class, 'postReset']);
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
    Route::post('/user/updateprofile/{id}', [UserController::class, 'updateProfile']);

    // MainCategory

    Route::get('/main-categories', [MainCategoryController::class, 'index']);
    Route::post('/main-categories', [MainCategoryController::class, 'store']);
    Route::get('/main-categories/edit/{id}', [MainCategoryController::class, 'edit']);
    Route::put('/main-categories/update/{id}', [MainCategoryController::class, 'update']);
    Route::delete('/main-categories/delete/{id}', [MainCategoryController::class, 'destroy']);

});
