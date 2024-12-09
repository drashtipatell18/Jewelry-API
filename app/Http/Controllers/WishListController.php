<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WishList;

use Illuminate\Support\Facades\Validator;
class WishListController extends Controller
{
    public function getAllWishLists()
    {
        $wishLists = WishList::with('product', 'customer')->get();

        return response()->json(
            [
                'status' => true,
                'message' => 'Wish Lists fetched successfully',
                'data' => $wishLists->map(function ($wishList) {
                    // Decode and process product images only if they exist
                    $imageUrls = [];
                    if (!empty($wishList->product->image)) {
                        $imageUrls = array_map(function ($imageName) {
                            return url('images/products/' . $imageName);
                        }, json_decode($wishList->product->image, true));
                    }

                    return [
                        'id' => $wishList->id,
                        'customer_id' => $wishList->customer_id,
                        'customer_name' => $wishList->customer->name ?? null,
                        'product_id' => $wishList->product_id,
                        'product_name' => $wishList->product->product_name ?? null,
                        'product_image' => $imageUrls,
                        'product_price' => $wishList->product->price ?? null,
                    ];
                })
            ],
            200
        );
    }

    public function getWishListById($id)
    {
        $wishList = WishList::with('product','customer')->find($id);
        $imageUrls = [];
        if (!empty($wishList->product->image)) {
            $imageUrls = array_map(function ($imageName) {
                return url('images/products/' . $imageName);
            }, json_decode($wishList->product->image, true));
        }
        return response()->json(
            [
                'status' => true,
                'message' => 'Wish List fetched successfully',
                'data' => [
                    'id' => $wishList->id,
                    'customer_id' => $wishList->customer_id,
                    'customer_name' => isset($wishList->customer->name) ? $wishList->customer->name : null,
                    'product_id' => $wishList->product_id,
                    'product_name' => isset($wishList->product->product_name) ? $wishList->product->product_name : null,
                    'product_image' =>  $imageUrls,
                    'product_price' => isset($wishList->product->price) ? $wishList->product->price : null
                ]
            ], 200);
    }

    public function createWishList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $wishList = WishList::create([
            'customer_id' => $request->input('customer_id'),
            'product_id' => $request->input('product_id'),
        ]);
        return response()->json(
            [
                'status' => true,
                'message' => 'Wish List created successfully',
                'data' => $wishList
            ], 201);
    }

    public function wishlistUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $wishList = WishList::find($id);
        $wishList->update([
            'customer_id' => $request->input('customer_id'),
            'product_id' => $request->input('product_id'),
        ]);
        return response()->json(
            [
                'status' => true,
                'message' => 'Wish List updated successfully',
                'data' => [
                    'id' => $wishList->id,
                    'customer_id' => $wishList->customer_id,
                    'customer_name' => isset($wishList->customer->name) ? $wishList->customer->name : null,
                    'product_id' => $wishList->product_id,
                    'product_name' => isset($wishList->product->product_name) ? $wishList->product->product_name : null
                ]
            ], 200);
    }

    public function deleteWishList($id)
    {
        $wishList = WishList::find($id);
        $wishList->delete();
        return response()->json([
            'status' => true,
            'message' => 'Wish List deleted successfully'
        ], 200);
    }
}
