<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function createReview(Request $request)
    {
        if ($request->user()->role_id !== 2) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'description' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'date' => 'required|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $review = Review::create([
            'customer_id' => $request->input('customer_id'),
            'product_id' => $request->input('product_id'),
            'description' => $request->input('description'),
            'rating' => $request->input('rating'),
            'date' => Carbon::parse($request->input('date'))->format('Y-m-d'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review
        ], 200);
    }

    // public function getAllReviews()
    // {
    //     $reviews = Review::all();
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Reviews fetched successfully',
    //         'data' => $reviews
    //     ], 200);
    // }
//     public function getAllReviews()
// {
//     $reviews = Review::with(['customer', 'product'])->get(); // Eager load customer and product relationships

//     $response = $reviews->map(function ($review) {
//         return [
//             'id' => $review->id,
//             'date' => $review->date, // Assuming a field 'review_text'
//             'description' => $review->description, // Assuming a field 'review_text'
//             'rating' => $review->rating,     // Assuming a field 'rating'
//             'customer_id' => $review->customer_id,     // Assuming a field 'rating'
//             'product_id' => $review->product_id,     // Assuming a field 'rating'
//             'customer_name' =>isset( $review->customer->name)? $review->customer->name:'', // Assuming a 'name' field in Customer model
//             'product_name' => isset($review->product->product_name)?$review->product->product_name:'',   // Assuming a 'name' field in Product model
//         ];
//     });

//     return response()->json([
//         'success' => true,
//         'message' => 'Reviews fetched successfully',
//         'data' => $response
//     ], 200);
// }
public function getAllReviews()
{
    $reviews = Review::with(['customer', 'product'])->get(); // Eager load customer and product relationships

    $response = $reviews->map(function ($review) {
        $productImages = [];
        if (!empty($review->product->image)) {
            // Handle comma-separated or JSON-encoded image strings
            if (is_string($review->product->image)) {
                // Check if it's JSON-encoded
                $decodedImages = json_decode($review->product->image, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $productImages = array_map(function ($image) {
                        return url('images/' . $image);
                    }, $decodedImages);
                } else {
                    // Assume it's a comma-separated string
                    $imageArray = explode(',', $review->product->image);
                    $productImages = array_map(function ($image) {
                        return url('images/' . trim($image));
                    }, $imageArray);
                }
            }
        }

        return [
            'id' => $review->id,
            'date' => $review->date, // Assuming a field 'date' exists
            'description' => $review->description, // Assuming a field 'description' exists
            'rating' => $review->rating, // Assuming a field 'rating' exists
            'customer_id' => $review->customer_id, // Assuming a field 'customer_id' exists
            'product_id' => $review->product_id, // Assuming a field 'product_id' exists
            'customer_name' => $review->customer->name ?? '', // Assuming 'name' field exists in Customer model
            'product_name' => $review->product->product_name ?? '', // Assuming 'product_name' exists in Product model
            'customer_image' => $review->customer->image 
                ? url('images/' . $review->customer->image) 
                : '',
            'product_images' => $productImages, // Array of image URLs
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'Reviews fetched successfully',
        'data' => $response
    ], 200);
}

    public function getReviewById($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Review fetched successfully',
            'data' => $review
        ], 200);
    }

    public function deleteReview($id)
    {
        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }
        $review->delete();
        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
            'data' => $review
        ], 200);
    }

    public function AllDeleteReview(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Review::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All Reviews deleted successfully'
        ], 200);
    }

    public function DateSearchReview(Request $request)
    {
        $reviews = Review::whereDate('date', '=', $request->input('date'))
                ->get();
        return response()->json([
            'success' => true,
            'message' => 'Reviews fetched successfully',
            'data' => $reviews
        ], 200);
    }
}
