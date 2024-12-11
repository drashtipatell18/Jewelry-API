<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\FAQ;
use App\Models\LeaveUSMeassage;
use App\Models\Offer;
use App\Models\Order_Product;
use App\Models\Order;
use App\Models\PrivacyPolicy;
use App\Models\Product;
use App\Models\ProductOffer;
use App\Models\ReasonForCancellation;
use App\Models\ReturnOrder;
use App\Models\Review;
use App\Models\Role;
use App\Models\Size;
use App\Models\Stock;
use App\Models\SubCategory;
use App\Models\SubFAQ;
use App\Models\TermCondition;
use App\Models\User;
use App\Models\Wishlist;

class SearchController extends Controller
{
    public function search(Request $request) {
        $term = $request->input('term');
        $results = [];

        // Search in each model
        $results['categories'] = Category::where('name', 'LIKE', "%$term%")
        ->orWhere('status', 'LIKE', "%$term%")
        ->get();
        $results['products'] = Product::where('product_name', 'LIKE', "%$term%")
        ->orWhere('metal_color', 'LIKE', "%$term%")
        ->orWhere('metal', 'LIKE', "%$term%")
        ->orWhere('diamond_color', 'LIKE', "%$term%")
        ->orWhere('diamond_quality', 'LIKE', "%$term%")
        ->orWhere('clarity', 'LIKE', "%$term%")
        ->orWhere('price', 'LIKE', "%$term%")
        ->orWhere('status', 'LIKE', "%$term%")
        ->get();

        $results['orders'] = Order::where('order_number', 'LIKE', "%$term%")
        ->orWhere('order_date', 'LIKE', "%$term%")
        ->orWhere('total_amount', 'LIKE', "%$term%")
        ->orWhere('order_status', 'LIKE', "%$term%")
        ->orWhere('invoice_number', 'LIKE', "%$term%")
        ->orWhere('qty', 'LIKE', "%$term%")
        ->orWhere('discount', 'LIKE', "%$term%")
        ->get();
        $results['coupons'] = Coupon::where('code', 'LIKE', "%$term%")
        ->orWhere('name', 'LIKE', "%$term%")
        ->orWhere('description', 'LIKE', "%$term%")
        ->orWhere('type', 'LIKE', "%$term%")
        ->orWhere('price', 'LIKE', "%$term%")
        ->orWhere('start_date', 'LIKE', "%$term%")
        ->orWhere('end_date', 'LIKE', "%$term%")
        ->orWhere('status', 'LIKE', "%$term%")
        ->get();
        $results['faqs'] = FAQ::where('name', 'LIKE', "%$term%")
        ->get();
        $results['leave_u_s_meassages'] = LeaveUSMeassage::where('name', 'LIKE', "%$term%")
        ->orWhere('email', 'LIKE', "%$term%")
        ->orWhere('subject', 'LIKE', "%$term%")
        ->orWhere('message', 'LIKE', "%$term%")
        ->get();
        $results['offers'] = Offer::where('name', 'LIKE', "%$term%")
        ->orWhere('type', 'LIKE', "%$term%")
        ->orWhere('image', 'LIKE', "%$term%")
        ->orWhere('button_text', 'LIKE', "%$term%")
        ->orWhere('discount', 'LIKE', "%$term%")
        ->orWhere('description', 'LIKE', "%$term%")
        ->orWhere('status', 'LIKE', "%$term%")
        ->orWhere('start_date', 'LIKE', "%$term%")
        ->orWhere('end_date', 'LIKE', "%$term%")
        ->get();
        $results['privacy_policies'] = PrivacyPolicy::where('title', 'LIKE', "%$term%")
        ->orWhere('description', 'LIKE', "%$term%")
        ->get();
        $results['product_offers'] = ProductOffer::whereHas('product', function ($query) use ($term) {
            $query->where('name', 'LIKE', "%$term%");
            $query->orWhere('code', 'LIKE', "%$term%");
            $query->orWhere('start_date', 'LIKE', "%$term%");
            $query->orWhere('end_date', 'LIKE', "%$term%");
            $query->orWhere('minimum_purchase', 'LIKE', "%$term%");
            $query->orWhere('minimum_discount', 'LIKE', "%$term%");
            $query->orWhere('status', 'LIKE', "%$term%");
            $query->orWhere('description', 'LIKE', "%$term%");
            $query->orWhere('price', 'LIKE', "%$term%");
        })->get();
        $results['return_orders'] = ReturnOrder::whereHas('order', function ($query) use ($term) {
            $query->where('order_number', 'LIKE', "%$term%");
            $query->orWhere('return_date', 'LIKE', "%$term%");
            $query->orWhere('return_status', 'LIKE', "%$term%");
            $query->orWhere('price', 'LIKE', "%$term%");
        })->get();
        $results['reviews'] = Review::whereHas('product', function ($query) use ($term) {
            $query->orWhere('description', 'LIKE', "%$term%");
            $query->orWhere('rating', 'LIKE', "%$term%");
            $query->orWhere('date', 'LIKE', "%$term%");
            $query->orWhere('status', 'LIKE', "%$term%");
        })->get();
        $results['roles'] = Role::where('name', 'LIKE', "%$term%")->get();
        $results['sizes'] = Size::where('name', 'LIKE', "%$term%")->get();
        $results['stocks'] = Stock::whereHas('product', function ($query) use ($term) {
            $query->where('product_name', 'LIKE', "%$term%");
            $query->orWhere('metal_color', 'LIKE', "%$term%");
            $query->orWhere('metal', 'LIKE', "%$term%");
            $query->orWhere('diamond_color', 'LIKE', "%$term%");
            $query->orWhere('diamond_quality', 'LIKE', "%$term%");
            $query->orWhere('clarity', 'LIKE', "%$term%");
            $query->orWhere('price', 'LIKE', "%$term%");
            $query->orWhere('status', 'LIKE', "%$term%");
        })->get();
        $results['sub_categories'] = SubCategory::whereHas('category', function ($query) use ($term) {
            $query->where('name', 'LIKE', "%$term%");
            $query->orWhere('status', 'LIKE', "%$term%");
        })->get();
        $results['sub_faqs'] = SubFAQ::whereHas('faq', function ($query) use ($term) {
            $query->where('question', 'LIKE', "%$term%");
            $query->orWhere('answer', 'LIKE', "%$term%");
        })->get();
        $results['term_conditions'] = TermCondition::where('description', 'LIKE', "%$term%")
        ->orWhere('title', 'LIKE', "%$term%")
        ->get();
        $results['users'] = User::where('name', 'LIKE', "%$term%")
        ->orWhere('email', 'LIKE', "%$term%")
        ->orWhere('phone', 'LIKE', "%$term%")
        ->orWhere('address', 'LIKE', "%$term%")
        ->get();
        $results['wishlists'] = Wishlist::whereHas('product', function ($query) use ($term) {
            $query->where('product_name', 'LIKE', "%$term%");
            $query->orWhere('metal_color', 'LIKE', "%$term%");
            $query->orWhere('metal', 'LIKE', "%$term%");
            $query->orWhere('diamond_color', 'LIKE', "%$term%");
            $query->orWhere('diamond_quality', 'LIKE', "%$term%");
            $query->orWhere('clarity', 'LIKE', "%$term%");
            $query->orWhere('price', 'LIKE', "%$term%");
            $query->orWhere('status', 'LIKE', "%$term%");
        })->get();

        return response()->json([
            'success' => true,
            'message' => 'Search results fetched successfully',
            'data' => $results
        ], 200);
    }
}

