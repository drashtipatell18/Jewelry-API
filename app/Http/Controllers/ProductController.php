<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;

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
            'status' => 'required|in:active,inactive',
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
            'status' => $request->input('status'),
            'image' => $imageNames ? json_encode($imageNames) : null, // Store as JSON or null if no images
        ]);

        $imageUrls = array_map(function($imageName) {
            return url('images/products/' . $imageName);
        }, $imageNames);
        $catName = $product->category_name = Category::find($product->category_id)->name;
        $subCatName = $product->sub_category_name = SubCategory::find($product->sub_category_id)->name;

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product created successfully',
                'data' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                    'category_name' =>$catName,
                    'sub_category_name' => $subCatName,
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
                    'status' => $product->status,
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
         $products = Product::with(['category' => function ($query) {$query->withTrashed(); },
                                    'subCategory' => function ($query) {$query->withTrashed(); },])->get();
        $productData = [];

        foreach($products as $product) {
            $imageUrls = json_decode($product->image, true);
            if (!is_array($imageUrls)) {
                $imageUrls = [];
            }
            $imageUrls = array_map(function($imageName) {
                return url('images/products/' . $imageName);
            }, $imageUrls);

            $productData[] = [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                'category_name' => $product->category ? $product->category->name : null,
                'sub_category_name' => $product->subCategory ? $product->subCategory->name : null,
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
                'status' => $product->status,
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

    public function activeProduct()
    {
        $products = Product::where('status', 'active')->get();

        $productData = $products->map(function ($product) {
            $imageUrls = json_decode($product->image, true);

            // Ensure $imageUrls is an array
            if (!is_array($imageUrls)) {
                $imageUrls = [];
            }

            // Generate full image URLs
            $imageUrls = array_map(function ($imageName) {
                return url('images/products/' . $imageName);
            }, $imageUrls);

            return [
                'id' => $product->id,
                'status' => $product->status,
                  'category_id'=> $product->category_id,
                'sub_category_id'=> $product->sub_category_id,
                'product_name' => $product->product_name,
                'category_name' => $product->category ? $product->category->name : null,
                'sub_category_name' => $product->subCategory ? $product->subCategory->name : null,
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
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Active products retrieved successfully',
            'data' => $productData,
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
         $catName = $product->category_name = Category::find($product->category_id)->name;
        $subCatName = $product->sub_category_name = SubCategory::find($product->sub_category_id)->name;
        return response()->json([
            'status' => 'success',
            'message' => 'Product fetched successfully',
            'data' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                      'category_name' =>$catName,
                    'sub_category_name' => $subCatName,
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
                    'status' => $product->status,
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
            'status' => 'required|in:active,inactive',
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
        if ($request->image) {
            $images = $request->image; // Get the entire image array
            $imageNames = []; // Initialize $imageNames
            // Loop through the images array
            foreach ($images as $index => $image) {
                if (isset($image['url'])) {
                    // If the image is a URL, keep it unchanged
                    $imageNames[] = basename($image['url']); // Extract the filename from the URL

                } elseif (isset($image['file'])) {
                    // If the image is a file, process it
                    $uploadedImage = $image['file'];
                    $imageName = uniqid() . '.' . $uploadedImage->getClientOriginalExtension();
                    $uploadedImage->move(public_path('images/products'), $imageName);
                    $imageNames[] = $imageName; // Add new image name to the array
                }
            }
        }

        $product->update([
            'product_name' => $request->input('product_name'),
            'category_id' => $request->input('category_id'),
            'sub_category_id' => $request->input('sub_category_id'),
            'metal_color' => $request->input('metal_color'),
            'metal' => $request->input('metal'),
            'status' => $request->input('status'),
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
           $catName = $product->category_name = Category::find($product->category_id)->name;
        $subCatName = $product->sub_category_name = SubCategory::find($product->sub_category_id)->name;

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'category_id' => $product->category_id,
                'sub_category_id' => $product->sub_category_id,
                  'category_name' =>$catName,
                    'sub_category_name' => $subCatName,
                'metal_color' => $product->metal_color,
                'metal' => $product->metal,
                'status' => $product->status,
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
   $catName = $product->category_name = Category::find($product->category_id)->name;
        $subCatName = $product->sub_category_name = SubCategory::find($product->sub_category_id)->name;
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully',
            'data' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'category_id' => $product->category_id,
                    'sub_category_id' => $product->sub_category_id,
                      'category_name' =>$catName,
                    'sub_category_name' => $subCatName,
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

    public function AllDeleteProduct(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Product::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All Products deleted successfully'
        ], 200);
    }

    public function updateStatusProduct($id, Request $request)
    {
        $product = Product::find($id);
        if(!$product){
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        $product->update(['status' => $request->input('status')]);
        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully'
        ], 200);
    }

    public function filterProducts(Request $request)
    {
        $query = Product::query();

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by sub-category
        if ($request->has('sub_category_id') && $request->sub_category_id) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by price range
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        // Sort by price
        if ($request->has('sort') && $request->sort) {
            if ($request->sort === 'price_low_high') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort === 'price_high_low') {
                $query->orderBy('price', 'desc');
            }
        }

        // Filter for Best Selling Products
        if ($request->has('best_selling') && $request->best_selling) {
            $query->select('product_name', DB::raw('SUM(price) as total_sales'))
                  ->groupBy('product_name')
                  ->orderBy('total_sales', 'desc'); // Assuming you have a sales_count field
        }

        // Filter for Low Stock Products
        if ($request->has('low_stock') && $request->low_stock) {
            $query->where('qty', '<=', 5); // Adjust the threshold as needed
        }

        $products = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Filtered products fetched successfully',
            'data' => $products
        ], 200);
    }
}
