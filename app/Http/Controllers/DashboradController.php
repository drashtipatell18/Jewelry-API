<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use App\Models\Category;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
class DashboradController extends Controller
{
    public function dashboard()
    {
        // Calculate total revenue
        $totalRevenue = Order::sum('total_amount');

        // Count total orders
        $totalOrders = Order::count();

        // Count total customers
        $totalCustomers = User::where('role_id', '!=', 1)->count(); // Assuming role_id 1 is for admin

        // Count total products
        $totalProducts = Product::count();

        // Top Category
        $topCategory = Product::select('category_id', DB::raw('count(*) as product_count'))
        ->groupBy('category_id')
        ->orderBy('product_count', 'desc')
        ->take(5) // Limit to 5 results
        ->first();

        $topCategoryName = $topCategory ? Category::find($topCategory->category_id)->name : null;

        // Fetch all reviews with customer details
        $reviews = Review::with('customer')->select('customer_id', 'description', 'rating')->get();

        // Structure reviews by customer, limiting to one review per customer
        // $structuredReviews = $reviews->groupBy('customer_id')->map(function ($group) {
        //     // $reviews = $group->take(3);
        //     return $group->map(function ($review) {
        //         return [
        //             'customer_name' => $review->customer->name,
        //             'description' => $review->description,
        //             'rating' => $review->rating,
        //         ];
        //     });
        // })->values();
$structuredReviews = $reviews->flatten()->map(function ($review) {
    return [
        'customer_name' => $review->customer->name,
        'description' => $review->description,
        'rating' => $review->rating,
    ];
})->values();
        // Fetch all products with their stock quantity
        $productsWithStock = Product::all();

        // Calculate total stock quantity
        $stock = Stock::all();

        // Fetch top sales location
        $topSalesLocation = Order::with('deliveryAddress')->get()->pluck('deliveryAddress.address')->mode();

        // Get product name with max quantity
        $productWithMaxQty = Product::orderBy('qty', 'desc')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'total_customers' => $totalCustomers,
                'total_products' =>[
                    'total_products' => $totalProducts,
                    'product_with_max_qty' => $productWithMaxQty ? $productWithMaxQty->product_name : null,
                ],
                'reviews' => $structuredReviews,
                'stock' => $stock,
                'top_category' => [
                    'category_name' => $topCategoryName,
                    'product_count' => $topCategory ? $topCategory->product_count : null,
                ],
                'top_sales_location' => $topSalesLocation,
            ],
        ], 200);
    }
}
