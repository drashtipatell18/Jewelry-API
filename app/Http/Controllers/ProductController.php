<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    public function createProduct(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'metal_color' => 'required',
            'metal' => 'required',
            'diamond_color' => 'required',
            'diamond_quality' => 'required',
            'clarity' => 'required',
            'size_id' => 'required|exists:sizes,id',
            'weight' => 'required|numeric|min:0',
            'no_of_diamonds' => 'required|integer|min:0',
            'diamond_setting' => 'required',
            'diamond_shape' => 'required',
            'collection' => 'required',
            'gender' => 'required',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'image' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $imageNames = [];
        if ($request->hasFile('image')) {
            $images = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];
            foreach ($images as $image) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                $imageNames[] = $imageName;
            }
        }

        $product = Product::create([
            'product_name' => $request->input('product_name'),
            'category_id' => $request->input('category_id'),
            'sub_category_id' => $request->input('sub_category_id'),
            'metal_color' => $request->input('metal_color'),
            'metal' => $request->input('metal'),
            'diamond_color' => $request->input('diamond_color'),
            'diamond_quality' => json_encode($request->input('diamond_quality')),
            'clarity' => $request->input('clarity'),
            'size_name' => $request->input('size_name'),
            'size_id' => $request->input('size_id'),
            'weight' => $request->input('weight'),
            'no_of_diamonds' => $request->input('no_of_diamonds'),
            'diamond_setting' => json_encode($request->input('diamond_setting')),
            'diamond_shape' => $request->input('diamond_shape'),
            'collection' => $request->input('collection'),
            'gender' => $request->input('gender'),
            'description' => $request->input('description'),
            'qty' => $request->input('qty'),
            'price' => $request->input('price'),
            'discount' => $request->input('discount'),
            'image' => $imageNames ? json_encode($imageNames) : null, // Store as JSON or null if no images
        ]);

        $imageUrls = array_map(function($imageName) {
            return url('images/products/' . $imageName);
        }, $imageNames);

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'metal_color' => $product->metal_color,
                    'metal' => $product->metal,
                    'diamond_color' => $product->diamond_color,
                    'diamond_quality' => $product->diamond_quality,
                    'clarity' => $product->clarity,
                    'size_name' => $product->size_name,
                    'size_id' => $product->size_id,
                    'weight' => $product->weight,
                    'no_of_diamonds' => $product->no_of_diamonds,
                    'diamond_setting' => $product->diamond_setting,
                    'diamond_shape' => $product->diamond_shape,
                    'collection' => $product->collection,
                    'gender' => $product->gender,
                    'description' => $product->description,
                    'qty' => $product->qty,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'images' => $imageUrls,
                ]
            ], 200);
    }

    public function getAllProducts()
    {
        $products = Product::all();
        $productData = [];

        foreach($products as $product) {
            $imageUrls = json_decode($product->image, true);
            $imageUrls = array_map(function($imageName) {
                return url('images/products/' . $imageName);
            }, $imageUrls);

            $productData[] = [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'metal_color' => $product->metal_color,
                'metal' => $product->metal,
                'diamond_color' => $product->diamond_color,
                'diamond_quality' => json_decode($product->diamond_quality),
                'clarity' => $product->clarity,
                'size_name' => $product->size_name,
                'size_id' => $product->size_id,
                'weight' => $product->weight,
                'no_of_diamonds' => $product->no_of_diamonds,
                'diamond_setting' => json_decode($product->diamond_setting),
                'diamond_shape' => $product->diamond_shape,
                'collection' => $product->collection,
                'gender' => $product->gender,
                'description' => $product->description,
                'qty' => $product->qty,
                'price' => $product->price,
                'discount' => $product->discount,
                'images' => $imageUrls,
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Products fetched successfully',
            'data' => $productData
        ], 200);
    }

    public function getProductById($id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        $imageUrls = json_decode($product->image, true);
        $imageUrls = array_map(function($imageName) {
            return url('images/products/' . $imageName);
        }, $imageUrls);

        return response()->json([
            'status' => 'success',
            'message' => 'Product fetched successfully',
            'data' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'metal_color' => $product->metal_color,
                    'metal' => $product->metal,
                    'diamond_color' => $product->diamond_color,
                    'diamond_quality' => json_decode($product->diamond_quality),
                    'clarity' => $product->clarity,
                    'size_name' => $product->size_name,
                    'size_id' => $product->size_id,
                    'weight' => $product->weight,
                    'no_of_diamonds' => $product->no_of_diamonds,
                    'diamond_setting' => json_decode($product->diamond_setting),
                    'diamond_shape' => $product->diamond_shape,
                    'collection' => $product->collection,
                    'gender' => $product->gender,
                    'description' => $product->description,
                    'qty' => $product->qty,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'images' => $imageUrls,
            ]
        ], 200);
    }

    public function updateProduct($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
        }
        $imageNames = []; // Initialize $imageNames

        $validator = Validator::make($request->all(), [
            'product_name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'metal_color' => 'required',
            'metal' => 'required',
            'diamond_color' => 'required',
            'diamond_quality' => 'required',
            'clarity' => 'required',
            'size_id' => 'required|exists:sizes,id',
            'weight' => 'required|numeric|min:0',
            'no_of_diamonds' => 'required|integer|min:0',
            'diamond_setting' => 'required',
            'diamond_shape' => 'required',
            'collection' => 'required',
            'gender' => 'required',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'image' => 'array',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()], 400);
        }

        $product = Product::find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Check if new images are uploaded
        if ($request->hasFile('image')) {
            $images = is_array($request->file('image')) ? $request->file('image') : [$request->file('image')];
            foreach ($images as $image) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);
                $imageNames[] = $imageName;
            }
        } else {
            // If no new images, retain existing images
            $existingImages = json_decode($product->image, true);
            $imageNames = $existingImages ? $existingImages : [];
        }

        $product->update([
            'product_name' => $request->input('product_name'),
            'category_id' => $request->input('category_id'),
            'sub_category_id' => $request->input('sub_category_id'),
            'metal_color' => $request->input('metal_color'),
            'metal' => $request->input('metal'),
            'diamond_color' => $request->input('diamond_color'),
            'diamond_quality' => json_encode($request->input('diamond_quality')),
            'clarity' => $request->input('clarity'),
            'size_id' => $request->input('size_id'),
            'weight' => $request->input('weight'),
            'no_of_diamonds' => $request->input('no_of_diamonds'),
            'diamond_setting' => json_encode($request->input('diamond_setting')),
            'diamond_shape' => $request->input('diamond_shape'),
            'collection' => $request->input('collection'),
            'gender' => $request->input('gender'),
            'description' => $request->input('description'),
            'qty' => $request->input('qty'),
            'price' => $request->input('price'),
            'discount' => $request->input('discount'),
            'image' => $imageNames ? json_encode($imageNames) : null,
        ]);

        $imageUrls = array_map(function($imageName) {
            return url('images/products/' . $imageName);
        }, $imageNames);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'metal_color' => $product->metal_color,
                'metal' => $product->metal,
                'diamond_color' => $product->diamond_color,
                'diamond_quality' => $product->diamond_quality,
                'clarity' => $product->clarity,
                'size_name' => $product->size_name,
                'size_id' => $product->size_id,
                'weight' => $product->weight,
                'no_of_diamonds' => $product->no_of_diamonds,
                'diamond_setting' => $product->diamond_setting,
                'diamond_shape' => $product->diamond_shape,
                'collection' => $product->collection,
                'gender' => $product->gender,
                'description' => $product->description,
                'qty' => $product->qty,
                'price' => $product->price,
                'discount' => $product->discount,
                'images' => $imageUrls,
            ]
        ], 200);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
        }
        $product->delete();
        $imageUrls = json_decode($product->image, true);
        $imageUrls = array_map(function($imageName) {
            return url('images/products/' . $imageName);
        }, $imageUrls);

        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
            'data' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'metal_color' => $product->metal_color,
                    'metal' => $product->metal,
                    'diamond_color' => $product->diamond_color,
                    'diamond_quality' => json_decode($product->diamond_quality),
                    'clarity' => $product->clarity,
                    'size_name' => $product->size_name,
                    'size_id' => $product->size_id,
                    'weight' => $product->weight,
                    'no_of_diamonds' => $product->no_of_diamonds,
                    'diamond_setting' => json_decode($product->diamond_setting),
                    'diamond_shape' => $product->diamond_shape,
                    'collection' => $product->collection,
                    'gender' => $product->gender,
                    'description' => $product->description,
                    'qty' => $product->qty,
                    'price' => $product->price,
                    'discount' => $product->discount,
                    'images' => $imageUrls,
            ]
        ], 200);
    }
}