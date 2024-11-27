<?php

use App\Models\MainCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
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

    // Category
    Route::post('/categories/create', [CategoryController::class, 'createCategory']);
    Route::get('/categories/getall', [CategoryController::class, 'getAllCategory']);
    Route::get('/categories/get/{id}', [CategoryController::class, 'getCategory']);
    Route::post('/categories/update/{id}', [CategoryController::class, 'updateCategory']);
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'deleteCategory']);
    Route::post('/categories/updatestatus/{id}', [CategoryController::class, 'updateStatusCategory']);
    Route::get('/categories/getallactive', [CategoryController::class, 'getAllActiveCategory']);
    Route::get('/categories/getallinactive', [CategoryController::class, 'getAllInactiveCategory']);
    // SubCategory
    Route::post('/subcategories/create', [SubCategoryController::class, 'createSubCategory']);
    Route::get('/subcategories/getall', [SubCategoryController::class, 'getAllSubCategory']);
    Route::get('/subcategories/get/{id}', [SubCategoryController::class, 'getSubCategory']);
    Route::post('/subcategories/update/{id}', [SubCategoryController::class, 'updateSubCategory']);
    Route::delete('/subcategories/delete/{id}', [SubCategoryController::class, 'deleteSubCategory']);
    Route::post('/subcategories/updatestatus/{id}', [SubCategoryController::class, 'updateStatusSubCategory']);
    Route::get('/subcategories/getallactive', [SubCategoryController::class, 'getAllActiveSubCategory']);
    Route::get('/subcategories/getallinactive', [SubCategoryController::class, 'getAllInactiveSubCategory']);
});
