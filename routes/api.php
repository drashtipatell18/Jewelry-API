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
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DeliveryAddressController;
use App\Http\Controllers\ProductOfferController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ReturnOrderController;
use App\Http\Controllers\DashboradController;
use App\Http\Controllers\WishListController;
use App\Http\Controllers\LeaveUSMeassageController;
use App\Http\Controllers\ReasonCancellationController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\SubFAQController;
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
Route::post('/user/create', [UserController::class, 'createUser']);
Route::get('/categories/getallactive', [CategoryController::class, 'getAllActiveCategory']);
Route::get('/subcategories/getallactive', [SubCategoryController::class, 'getAllActiveSubCategory']);
Route::get('/products/getallactive', [ProductController::class, 'activeProduct']);

// auth
Route::middleware('auth.api')->group(function () {

    // role
    Route::get('/roles', [RoleController::class, 'getRole']);
    Route::post('password/change', [UserController::class, 'changePassword']);

    // dashboard
    Route::get('/dashboard', [DashboradController::class, 'dashboard']);
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
    Route::delete('/categories/allDelete', [CategoryController::class, 'AllDeleteCategory']);
    Route::post('/categories/updatestatus/{id}', [CategoryController::class, 'updateStatusCategory']);
    Route::get('/categories/getallinactive', [CategoryController::class, 'getAllInactiveCategory']);

    // SubCategory

    Route::post('/subcategories/create', [SubCategoryController::class, 'createSubCategory']);
    Route::get('/subcategories/getall', [SubCategoryController::class, 'getAllSubCategory']);
    Route::get('/subcategories/get/{id}', [SubCategoryController::class, 'getSubCategory']);
    Route::post('/subcategories/update/{id}', [SubCategoryController::class, 'updateSubCategory']);
    Route::delete('/subcategories/delete/{id}', [SubCategoryController::class, 'deleteSubCategory']);
    Route::delete('/subcategories/allDelete', [SubCategoryController::class, 'AllDeleteSubCategory']);
    Route::post('/subcategories/updatestatus/{id}', [SubCategoryController::class, 'updateStatusSubCategory']);
    Route::get('/subcategories/getallinactive', [SubCategoryController::class, 'getAllInactiveSubCategory']);

    // Size
    Route::post('/sizes/create', [SizeController::class, 'createSize']);
    Route::get('/sizes/getall', [SizeController::class, 'getAllSizes']);
    Route::get('/sizes/get/{id}', [SizeController::class, 'getSizeById']);
    Route::post('/sizes/update/{id}', [SizeController::class, 'updateSize']);
    Route::delete('/sizes/delete/{id}', [SizeController::class, 'deleteSize']);
    Route::delete('/sizes/allDelete', [SizeController::class, 'AllDeleteSize']);


    // Order
    Route::post('/order/create', [OrderController::class, 'createOrder']);
    Route::get('/order/getall', [OrderController::class, 'getAllOrder']);
    Route::get('/order/get/{id}', [OrderController::class, 'getOrderById']);
    Route::post('/order/update/{id}', [OrderController::class, 'updateOrder']);
    Route::delete('/order/delete/{id}', [OrderController::class, 'deleteOrder']);
    Route::delete('/orders/allDelete', [OrderController::class, 'AllDeleteOrder']);
    Route::post('/order/updatestatus/{id}', [OrderController::class, 'updateStatusOrder']);
    Route::post('/order/invoice/{id}', [OrderController::class, 'invoiceOrder']);
    Route::post('/orderproduct/update/{id}', [OrderController::class, 'updateOrderProduct']);
    Route::delete('/orderproduct/delete/{id}', [OrderController::class, 'deleteOrderProduct']);

    // Return Order
    Route::post('/returnorder/create', [ReturnOrderController::class, 'createReturnOrder']);
    Route::get('/returnorder/getall', [ReturnOrderController::class, 'getAllReturnOrder']);
    Route::get('/returnorder/get/{id}', [ReturnOrderController::class, 'getReturnOrderById']);
    Route::post('/returnorder/update/{id}', [ReturnOrderController::class, 'updateReturnOrder']);
    Route::delete('/returnorder/delete/{id}', [ReturnOrderController::class, 'deleteReturnOrder']);
    Route::delete('/returnorder/allDelete', [ReturnOrderController::class, 'AllDeleteReturnOrder']);
    Route::post('/returnorder/updatestatus/{id}', [ReturnOrderController::class, 'updateStatusReturnOrder']);
    Route::get('/returnorder/getaccepted', [ReturnOrderController::class, 'getAllAcceptedReturnOrder']);
    Route::get('/returnorder/getrejected', [ReturnOrderController::class, 'getAllRejectedReturnOrder']);

    // Leave US Meassage

    Route::post('/leaveusmeassage/create', [LeaveUSMeassageController::class, 'createLeaveUSMeassage']);

    // FAQ
    Route::post('/faqs/create', [FAQController::class, 'createFAQ']);
    Route::get('/faqs/getall', [FAQController::class, 'getAllFAQ']);
    Route::get('/faqs/get/{id}', [FAQController::class, 'getFAQById']);
    Route::post('/faqs/update/{id}', [FAQController::class, 'updateFAQ']);
    Route::delete('/faqs/delete/{id}', [FAQController::class, 'deleteFAQ']);
    Route::delete('/faqs/allDelete', [FAQController::class, 'AllDeleteFAQ']);

    // Product
    Route::post('/products/create', [ProductController::class, 'createProduct']);
    Route::get('/products/getall', [ProductController::class, 'getAllProducts']);
    Route::get('/products/get/{id}', [ProductController::class, 'getProductById']);
    Route::post('/products/update/{id}', [ProductController::class, 'updateProduct']);
    Route::delete('/products/delete/{id}', [ProductController::class, 'deleteProduct']);
    Route::delete('/products/allDelete', [ProductController::class, 'AllDeleteProduct']);
    Route::post('/products/filter', [ProductController::class, 'filterProducts']);
    Route::post('/products/updatestatus/{id}', [ProductController::class, 'updateStatusProduct']);

    // Wish List
    Route::get('/wishlists/getall', [WishListController::class, 'getAllWishLists']);
    Route::post('/wishlists/create', [WishListController::class, 'createWishList']);
    Route::get('/wishlists/get/{id}', [WishListController::class, 'getWishListById']);
    Route::post('/wishlist/update/{id}', [WishListController::class, 'wishlistUpdate']);
    Route::delete('/wishlists/delete/{id}', [WishListController::class, 'deleteWishList']);

    // Sub FAQ
    Route::post('/subfaqs/create', [SubFAQController::class, 'createSubFAQ']);
    Route::get('/subfaqs/getall', [SubFAQController::class, 'getAllSubFAQ']);
    Route::get('/subfaqs/get/{id}', [SubFAQController::class, 'getSubFAQById']);
    Route::post('/subfaqs/update/{id}', [SubFAQController::class, 'updateSubFAQ']);
    Route::delete('/subfaqs/delete/{id}', [SubFAQController::class, 'deleteSubFAQ']);
    Route::delete('/subfaqs/allDelete', [SubFAQController::class, 'AllDeleteSubFAQ']);
    
    // Stock
    Route::post('/stocks/create', [StockController::class, 'createStock']);
    Route::get('/stocks/getall', [StockController::class, 'getAllStocks']);
    Route::get('/stocks/get/{id}', [StockController::class, 'getStockById']);
    Route::post('/stocks/update/{id}', [StockController::class, 'updateStock']);
    Route::delete('/stocks/delete/{id}', [StockController::class, 'deleteStock']);
    Route::delete('/stocks/allDelete', [StockController::class, 'AllDeleteStock']);
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
    Route::delete('/reviews/allDelete', [ReviewController::class, 'AllDeleteReview']);
    Route::post('/reviews/DateSearch', [ReviewController::class, 'DateSearchReview']);
    Route::post('/reviews/{id}/like-dislike', [ReviewController::class, 'updateLikeDislike']);

    // Product Offer
    Route::post('/productoffers/create', [ProductOfferController::class, 'createProductOffer']);
    Route::get('/productoffers/getall', [ProductOfferController::class, 'getAllProductOffers']);
    Route::get('/productoffers/get/{id}', [ProductOfferController::class, 'getProductOfferById']);
    Route::post('/productoffers/update/{id}', [ProductOfferController::class, 'updateProductOffer']);
    Route::delete('/productoffers/delete/{id}', [ProductOfferController::class, 'deleteProductOffer']);
    Route::delete('/productoffers/allDelete', [ProductOfferController::class, 'AllDeleteProductOffer']);
    Route::post('/productoffers/updatestatus/{id}', [ProductOfferController::class, 'updateStatusProductOffer']);
    Route::get('/productoffers/getallactive', [ProductOfferController::class, 'getAllActiveProductOffer']);
    Route::get('/productoffers/getallinactive', [ProductOfferController::class, 'getAllInactiveProductOffer']);
    Route::post('/productoffers/filter', [ProductOfferController::class, 'filterProductOffers']);

    // Offer
    Route::post('/offers/create', [OfferController::class, 'createOffer']);
    Route::get('/offers/getall', [OfferController::class, 'getAllOffers']);
    Route::get('/offers/get/{id}', [OfferController::class, 'getOfferById']);
    Route::post('/offers/update/{id}', [OfferController::class, 'updateOffer']);
    Route::delete('/offers/delete/{id}', [OfferController::class, 'deleteOffer']);
    Route::delete('/offers/allDelete', [OfferController::class, 'AllDeleteOffer']);
    Route::post('/offers/updatestatus/{id}', [OfferController::class, 'updateStatusOffer']);
    Route::get('/offers/getallactive', [OfferController::class, 'getAllActiveOffer']);
    Route::get('/offers/getallinactive', [OfferController::class, 'getAllInactiveOffer']);
    Route::post('/offers/filter', [OfferController::class, 'filterOffers']);

    // Coupon
    Route::post('/coupons/create', [CouponController::class, 'createCoupon']);
    Route::get('/coupons/getall', [CouponController::class, 'getAllCoupons']);
    Route::get('/coupons/get/{id}', [CouponController::class, 'getCouponById']);
    Route::post('/coupons/update/{id}', [CouponController::class, 'updateCoupon']);
    Route::delete('/coupons/delete/{id}', [CouponController::class, 'deleteCoupon']);
    Route::delete('/coupons/allDelete', [CouponController::class, 'AllDeleteCoupon']);
    Route::post('/coupons/filter', [CouponController::class, 'filterCoupons']);
    Route::post('/coupons/updatestatus/{id}', [CouponController::class, 'updateStatusCoupon']);
    // Delivery Address
    Route::post('/deliveryAddress/create', [DeliveryAddressController::class, 'createDeliveryAddress']);
    Route::get('/deliveryAddress/getall', [DeliveryAddressController::class, 'getAllDeliveryAddress']);
    Route::get('/deliveryAddress/get/{id}', [DeliveryAddressController::class, 'getDeliveryAddressById']);
    Route::post('/deliveryAddress/update/{id}', [DeliveryAddressController::class, 'updateDeliveryAddress']);
    Route::delete('/deliveryAddress/delete/{id}', [DeliveryAddressController::class, 'deleteDeliveryAddress']);

     // Reason For Cancellation
     Route::post('/reasonCancellation/create', [ReasonCancellationController::class, 'createReasonCancellation']);
     Route::get('/reasonCancellation/getall', [ReasonCancellationController::class, 'getAllReasonCancellation']);
     Route::get('/reasonCancellation/get/{id}', [ReasonCancellationController::class, 'getReasonCancellationById']);
     Route::post('/reasonCancellation/update/{id}', [ReasonCancellationController::class, 'updateReasonCancellation']);
     Route::delete('/reasonCancellation/delete/{id}', [ReasonCancellationController::class, 'deleteReasonCancellation']);


});
