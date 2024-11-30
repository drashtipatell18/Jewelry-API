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
    public function getAllReviews()
{
    $reviews = Review::with(['customer', 'product'])->get(); // Eager load customer and product relationships

    $response = $reviews->map(function ($review) {
        return [
            'id' => $review->id,
            'review' => $review->review_text, // Assuming a field 'review_text'
            'rating' => $review->rating,     // Assuming a field 'rating'
            'customer_name' => $review->customer->name, // Assuming a 'name' field in Customer model
            'product_name' => $review->product->name,   // Assuming a 'name' field in Product model
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
