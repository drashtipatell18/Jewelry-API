<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReviewController;

use App\Http\Controllers\DeliveryAddressController;
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
Route::post('password/otp', [UserController::class, 'sendOTP']);
Route::post('password/reset/{otp}', [UserController::class, 'postReset']);
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

    // Size
    Route::post('/sizes/create', [SizeController::class, 'createSize']);
    Route::get('/sizes/getall', [SizeController::class, 'getAllSizes']);
    Route::get('/sizes/get/{id}', [SizeController::class, 'getSizeById']);
    Route::post('/sizes/update/{id}', [SizeController::class, 'updateSize']);
    Route::delete('/sizes/delete/{id}', [SizeController::class, 'deleteSize']);

    // Order
    Route::post('/order/create', [OrderController::class, 'createOrder']);
    Route::get('/order/getall', [OrderController::class, 'getAllOrder']);
    Route::get('/order/get/{id}', [OrderController::class, 'getOrderById']);
    Route::post('/order/update/{id}', [OrderController::class, 'updateOrder']);
    Route::delete('/order/delete/{id}', [OrderController::class, 'deleteOrder']);

    // Product
    Route::post('/products/create', [ProductController::class, 'createProduct']);
    Route::get('/products/getall', [ProductController::class, 'getAllProducts']);
    Route::get('/products/get/{id}', [ProductController::class, 'getProductById']);
    Route::post('/products/update/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('/products/delete/{id}', [ProductController::class, 'deleteProduct']);

    // Stock
    Route::post('/stocks/create', [StockController::class, 'createStock']);
    Route::get('/stocks/getall', [StockController::class, 'getAllStocks']);
    Route::get('/stocks/get/{id}', [StockController::class, 'getStockById']);
    Route::post('/stocks/update/{id}', [StockController::class, 'updateStock']);
    Route::delete('/stocks/delete/{id}', [StockController::class, 'deleteStock']);
    Route::post('/stock/filter', [StockController::class, 'filterStock']);
    Route::post('/stocks/updatestatus/{id}', [StockController::class, 'updateStatusStock']);
    Route::get('/stocks/getoutofstock', [StockController::class, 'getOutStock']);
    Route::get('/stocks/getinstock', [StockController::class, 'getInStock']);
    Route::get('/stocks/getlowstock', [StockController::class, 'getLowStock']);

    // Review
    Route::post('/reviews/create', [ReviewController::class, 'createReview']);
    Route::get('/reviews/getall', [ReviewController::class, 'getAllReviews']);
    Route::get('/reviews/get/{id}', [ReviewController::class, 'getReviewById']);
    Route::delete('/reviews/delete/{id}', [ReviewController::class, 'deleteReview']);

    // Order
    Route::post('/orders/create', [OrderController::class, 'createOrder']);
    Route::get('/orders/getall', [OrderController::class, 'getAllOrders']);
    Route::get('/orders/get/{id}', [OrderController::class, 'getOrderById']);
    Route::post('/orders/update/{id}', [OrderController::class, 'updateOrder']);
    Route::delete('/orders/delete/{id}', [OrderController::class, 'deleteOrder']);

     // Delivery Address
    Route::post('/deliveryAddress/create', [DeliveryAddressController::class, 'createDeliveryAddress']);
    Route::get('/deliveryAddress/getall', [DeliveryAddressController::class, 'getAllDeliveryAddress']);
    Route::get('/deliveryAddress/get/{id}', [DeliveryAddressController::class, 'getDeliveryAddressById']);
    Route::post('/deliveryAddress/update/{id}', [DeliveryAddressController::class, 'updateDeliveryAddress']);
    Route::delete('/deliveryAddress/delete/{id}', [DeliveryAddressController::class, 'deleteDeliveryAddress']);

});
