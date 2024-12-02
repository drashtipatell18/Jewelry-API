<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductOffer;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class ProductOfferController extends Controller
{
    public function createProductOffer(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateProductOffer = Validator::make($request->all(), [
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'product_id' => 'required',
            'name' => 'required',
            'code' => 'required|unique:product_offers,code',
            'description' => 'nullable',
            'price' => 'required',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'minimum_purchase' => 'nullable',
            'minimum_discount' => 'nullable',
            'type' => 'required',
        ]);

        if($validateProductOffer->fails()){
            return response()->json($validateProductOffer->errors(), 401);
        }
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images/product_offers'), $imageName);
        }
        $productOffer = ProductOffer::create([
            'category_id' => $request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id'),
            'product_id' => $request->input('product_id'),
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'status' => $request->input('status'),
            'start_date' => Carbon::parse($request->input('start_date'))->format('Y-m-d'),
            'end_date' => Carbon::parse($request->input('end_date'))->format('Y-m-d'),
            'minimum_purchase' => $request->input('minimum_purchase'),
            'minimum_discount' => $request->input('minimum_discount'),
            'type' => $request->input('type'),
            'image' => $imageName,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Product Offer created successfully',
            'productOffer' => [
                'id' => $productOffer->id,
                'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                'category_name' => isset($productOffer->category) ? $productOffer->category->name : null,
                'subcategory_name' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                'product_name' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                'name' => $productOffer->name,
                'code' => $productOffer->code,
                'description' => $productOffer->description,
                'price' => $productOffer->price,
                'status' => $productOffer->status,
                'start_date' => $productOffer->start_date,
                'end_date' => $productOffer->end_date,
                'minimum_purchase' => $productOffer->minimum_purchase,
                'minimum_discount' => $productOffer->minimum_discount,
                'type' => $productOffer->type,
                'image' => url('images/product_offers/'.$productOffer->image),
            ]
        ], 200);
    }

    public function getAllProductOffers()
    {
        $productOffers = ProductOffer::with('category', 'subcategory', 'product')->get();
        return response()->json([
            'success' => true,
            'message' => 'All Product Offer fetched successfully',
            'productOffers' => $productOffers->map(function($productOffer){
                return [
                    'id' => $productOffer->id,
                    'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                    'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                    'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                    'category' => isset($productOffer->category) ? $productOffer->category->name : null,
                    'subcategory' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                    'product' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                    'name' => $productOffer->name,
                    'code' => $productOffer->code,
                    'description' => $productOffer->description,
                    'price' => $productOffer->price,
                    'status' => $productOffer->status,
                    'start_date' => $productOffer->start_date,
                    'end_date' => $productOffer->end_date,
                    'minimum_purchase' => $productOffer->minimum_purchase,
                    'minimum_discount' => $productOffer->minimum_discount,
                    'type' => $productOffer->type,
                    'image' => url('images/product_offers/'.$productOffer->image),
                ];
            })
        ], 200);
    }

    public function getProductOfferById($id)
    {
        $productOffer = ProductOffer::find($id);
        if(!$productOffer){
            return response()->json(['message' => 'Product Offer not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Product Offer fetched successfully',
            'productOffer' => [
                'id' => $productOffer->id,
                'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                'category_name' => isset($productOffer->category) ? $productOffer->category->name : null,
                'subcategory_name' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                'product_name' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                'name' => $productOffer->name,
                'code' => $productOffer->code,
                'description' => $productOffer->description,
                'price' => $productOffer->price,
                'status' => $productOffer->status,
                'start_date' => $productOffer->start_date,
                'end_date' => $productOffer->end_date,
                'minimum_purchase' => $productOffer->minimum_purchase,
                'minimum_discount' => $productOffer->minimum_discount,
                'type' => $productOffer->type,
                'image' => url('images/product_offers/'.$productOffer->image),
            ]
        ], 200);
    }

    public function updateProductOffer(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateProductOffer = Validator::make($request->all(), [
            'category_id' => 'required',
            'subcategory_id' => 'required',
            'product_id' => 'required',
            'name' => 'required',
            'code' => 'required|unique:product_offers,code,'.$id,
            'description' => 'nullable',
            'price' => 'required',
            'status' => 'required|in:active,inactive',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'minimum_purchase' => 'nullable',
            'minimum_discount' => 'nullable',
            'type' => 'required',
        ]);

        if($validateProductOffer->fails()){
            return response()->json($validateProductOffer->errors(), 401);
        }

        $productOffer = ProductOffer::with('category', 'subcategory', 'product')->find($id);
        $productOffer->update([
            'category_id' => $request->input('category_id'),
            'subcategory_id' => $request->input('subcategory_id'),
            'product_id' => $request->input('product_id'),
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'status' => $request->input('status'),
            'start_date' => Carbon::parse($request->input('start_date'))->format('Y-m-d'),
            'end_date' => Carbon::parse($request->input('end_date'))->format('Y-m-d'),
            'minimum_purchase' => $request->input('minimum_purchase'),
            'minimum_discount' => $request->input('minimum_discount'),
            'type' => $request->input('type'),
        ]);
        if(!$productOffer){
            return response()->json(['message' => 'Product Offer not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Product Offer updated successfully',
            'productOffer' => [
                'id' => $productOffer->id,
                'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                'category_name' => isset($productOffer->category) ? $productOffer->category->name : null,
                'subcategory_name' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                'product_name' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                'name' => $productOffer->name,
                'code' => $productOffer->code,
                'description' => $productOffer->description,
                'price' => $productOffer->price,
                'status' => $productOffer->status,
                'start_date' => $productOffer->start_date,
                'end_date' => $productOffer->end_date,
                'minimum_purchase' => $productOffer->minimum_purchase,
                'minimum_discount' => $productOffer->minimum_discount,
                'type' => $productOffer->type,
                'image' => url('images/product_offers/'.$productOffer->image),
            ]
        ], 200);
    }

    public function deleteProductOffer(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $productOffer = ProductOffer::find($id);
        $productOffer->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product Offer deleted successfully',
        ], 200);
    }

    public function AllDeleteProductOffer(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        ProductOffer::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All Product Offer deleted successfully',
        ], 200);
    }

    public function DateSearchProductOffer(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }


    }

    public function getAllActiveProductOffer()
    {
        $productOffers = ProductOffer::with('category', 'subcategory', 'product')->where('status', 'active')->get();
        return response()->json([
            'success' => true,
            'productOffers' => $productOffers->map(function($productOffer){
                return [
                    'id' => $productOffer->id,
                    'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                    'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                    'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                    'category_name' => isset($productOffer->category) ? $productOffer->category->name : null,
                    'subcategory_name' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                    'product_name' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                    'name' => $productOffer->name,
                    'code' => $productOffer->code,
                    'description' => $productOffer->description,
                    'price' => $productOffer->price,
                    'status' => $productOffer->status,
                    'image' => url('images/product_offers/'.$productOffer->image),
                    'start_date' => $productOffer->start_date,
                    'end_date' => $productOffer->end_date,
                    'minimum_purchase' => $productOffer->minimum_purchase,
                    'minimum_discount' => $productOffer->minimum_discount,
                    'type' => $productOffer->type,
                ];
            })
        ], 200);
    }

    public function getAllInactiveProductOffer()
    {
        $productOffers = ProductOffer::with('category', 'subcategory', 'product')->where('status', 'inactive')->get();
        return response()->json([
            'success' => true,
            'productOffers' => $productOffers->map(function($productOffer){
                return [
                    'id' => $productOffer->id,
                    'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                    'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                    'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                    'category_name' => isset($productOffer->category) ? $productOffer->category->name : null,
                    'subcategory_name' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                    'product_name' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                    'name' => $productOffer->name,
                    'code' => $productOffer->code,
                    'description' => $productOffer->description,
                    'price' => $productOffer->price,
                    'status' => $productOffer->status,
                    'image' => url('images/product_offers/'.$productOffer->image),
                    'start_date' => $productOffer->start_date,
                    'end_date' => $productOffer->end_date,
                    'minimum_purchase' => $productOffer->minimum_purchase,
                    'minimum_discount' => $productOffer->minimum_discount,
                    'type' => $productOffer->type,
                ];
            })
        ], 200);
    }

    public function updateStatusProductOffer(Request $request, $id)
{
    if ($request->user()->role_id !== 1) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $productOffer = ProductOffer::find($id);
    $productOffer->update([
        'status' => $request->input('status'),
    ]);

    // Get only category and product names
    $categoryName = $productOffer->category->name ?? null; // Assuming a relationship named 'category'
    $productName = $productOffer->product->product_name ?? null;

    return response()->json([
        'success' => true,
        'message' => 'Product Offer status updated successfully',
        'productOffer' => [
            'id' => $productOffer->id,
            'category' => $categoryName,
            'product' => $productName,
            'name' => $productOffer->name,
            'code' => $productOffer->code,
            'start_date' => $productOffer->start_date,
            'end_date' => $productOffer->end_date,
            'minimum_purchase' => $productOffer->minimum_purchase,
            'minimum_discount' => $productOffer->minimum_discount,
            'type' => $productOffer->type,
            'image' => $productOffer->image,
            'status' => $productOffer->status,
            'description' => $productOffer->description,
            'price' => $productOffer->price,
            'created_at' => $productOffer->created_at,
            'updated_at' => $productOffer->updated_at,
        ],
    ], 200);
}

    public function filterProductOffers(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = ProductOffer::with('category', 'subcategory', 'product');

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by start date
        if ($request->has('start_date') && $request->start_date) {
            $query->where('start_date', '>=', Carbon::parse($request->start_date));
        }

        // Filter by end date
        if ($request->has('end_date') && $request->end_date) {
            $query->where('end_date', '<=', Carbon::parse($request->end_date));
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by price range
        if ($request->has('price_min') && $request->has('price_max')) {
            $query->whereBetween('price', [$request->price_min, $request->price_max]);
        }

        // Filter by discount range
        if ($request->has('discount_min') && $request->has('discount_max')) {
            $query->whereBetween('minimum_discount', [$request->discount_min, $request->discount_max]);
        }

        $productOffers = $query->get();

        return response()->json([
            'success' => true,
            'productOffers' => $productOffers->map(function($productOffer){
                return [
                    'id' => $productOffer->id,
                    'category_id' => isset($productOffer->category_id) ? $productOffer->category_id : null,
                    'subcategory_id' => isset($productOffer->subcategory_id) ? $productOffer->subcategory_id : null,
                    'product_id' => isset($productOffer->product_id) ? $productOffer->product_id : null,
                    'category_name' => isset($productOffer->category) ? $productOffer->category->name : null,
                    'subcategory_name' => isset($productOffer->subcategory) ? $productOffer->subcategory->name : null,
                    'product_name' => isset($productOffer->product) ? $productOffer->product->product_name : null,
                    'status' => $productOffer->status,
                    'start_date' => $productOffer->start_date,
                    'end_date' => $productOffer->end_date,
                    'minimum_purchase' => $productOffer->minimum_purchase,
                    'minimum_discount' => $productOffer->minimum_discount,
                    'type' => $productOffer->type,
                    'image' => url('images/product_offers/'.$productOffer->image),
                    'price' => $productOffer->price,
                    'description' => $productOffer->description,
                ];
            }),
        ], 200);
    }
}
