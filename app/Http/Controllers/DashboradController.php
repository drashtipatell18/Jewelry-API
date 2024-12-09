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
        $topCategories = Product::select('category_id', DB::raw('count(*) as product_count'))
        ->groupBy('category_id')
        ->orderBy('product_count', 'desc')
          ->take(5) // Limit to 5 results
        ->get();

        // Update to structure top categories and counts into a single array
        $topCategoriesData = $topCategories->map(function ($category) {
            return [
                'category_name' => Category::find($category->category_id)->name ?? null,
                'product_count' => $category->product_count,
            ];
        })->toArray();

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
                'image' => 'https://shreekrishnaastrology.com/public/images/' . $review->customer->image
            ];
        })->values();

        // Fetch all products with their stock quantity
        $productsWithStock = Product::all();

        // Calculate total stock quantity
        // $stock = Stock::all();
        // $stock = Stock::with(['category', 'subCategory', 'product'])->get();
        $stock = Stock::with([
    'category' => function ($query) {
        $query->withTrashed(); // Include soft-deleted categories
    },
    'subCategory' => function ($query) {
        $query->withTrashed(); // Include soft-deleted subcategories
    },
    'product' => function ($query) {
        $query->withTrashed(); // Include soft-deleted products
    }
])->get();

// Transform stock data to include related details
        $stockData = $stock->map(function ($item) {
    if ($item->product) {
        $imageUrls = json_decode($item->product->image, true);
        if (!is_array($imageUrls)) {
            $imageUrls = [];
        }

        // Get the first image URL if available
        $firstImageUrl = !empty($imageUrls) ? url('images/products/' . $imageUrls[0]) : null;
    } else {
        $firstImageUrl = null; // If no product, set product image to null
    }
            return [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'category_name' => $item->category->name ?? null,
                'sub_category_id' => $item->sub_category_id,
                'sub_category_name' => $item->subCategory->name ?? null,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name ?? null,
                'product_image' => $firstImageUrl, // return only the first image URL or null
                'date' => $item->date,
                'status' => $item->status,
                'qty' => $item->qty,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'deleted_at' => $item->deleted_at,
            ];
        });




        // Fetch top sales location
        $topSalesLocation = Order::with('deliveryAddress')->get()->pluck('deliveryAddress.address')->mode();

        // Get product name with max quantity
        $productWithMaxQty = Product::orderBy('qty', 'desc')->first();

        $topProducts = DB::table('order_products')
        ->select('product_id', DB::raw('SUM(qty) as total_qty'))
        ->groupBy('product_id')
        ->orderBy('total_qty', 'desc')
        ->take(5)
        ->get();

        $totalSalesQty = DB::table('order_products')->sum('qty');
        $productStockData = Stock::all()->groupBy('product_id')->map(function ($items) {
            return $items->sum('qty'); // Sum up the stock quantities for each product
        });
        $topProductsData = $topProducts->map(function ($product) use ($totalSalesQty, $productStockData) {
            $productDetails = Product::find($product->product_id);
            $stockQty = $productStockData[$product->product_id] ?? 0; // Get stock quantity or default to 0
        
            // Calculate percentage of sales relative to available stock
            $salesPercentage = $stockQty > 0 ? round(($product->total_qty / $stockQty) * 100, 2) : 0;
        
            return [
                'product_name' => $productDetails->product_name ?? 'Unknown Product',
                'total_qty_sold' => $product->total_qty,
                'stock_qty' => $stockQty, // Include the stock quantity
                'sales_percentage' => $salesPercentage, // Add sales percentage based on stock
            ];
        })->toArray();

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
                'stock' => $stockData,
                'top_category' => $topCategoriesData,
                'top_products' => $topProductsData,
                'top_sales_location' => $topSalesLocation,
            ],
        ], 200);
    }
}
