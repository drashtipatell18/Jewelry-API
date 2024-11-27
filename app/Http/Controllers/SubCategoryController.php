<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategoryController extends Controller
{
    public function createSubCategory(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateSubCategory = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);
        if($validateSubCategory->fails()){
            return response()->json($validateSubCategory->errors(), 401);
        }

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images/subcategories'), $imageName);
        $imageName = url('images/subcategories/' . $imageName); // Generate the full URL for the image

        $subCategory = SubCategory::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'image' => $imageName,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'SubCategory created successfully',
            'subCategory' => [
                'id' => $subCategory->id,
                'name' => $subCategory->name,
                'category_id' => $subCategory->category->name,
                'image' => url('images/subcategories/' . $subCategory->image), // Return full URL here
            ]
        ], 200);
    }

    public function getAllSubCategory()
    {
        $subCategories = SubCategory::with('category')->get();
        return response()->json([
            'success' => true,
            'message' => 'SubCategories fetched successfully',
            'subCategories' => [
                'id' => $subCategories->id,
                'name' => $subCategories->name,
                'category_id' => $subCategories->category->name,
                'image' => url('images/subcategories/' . $subCategories->image), // Return full URL here
            ]
        ], 200);
    }

    public function getSubCategory($id)
    {
        $subCategory = SubCategory::find($id);
        if(!$subCategory){
            return response()->json(['message' => 'SubCategory not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'SubCategory fetched successfully',
            'subCategory' => [
                'id' => $subCategory->id,
                'name' => $subCategory->name,
                'category_id' => $subCategory->category->name,
                'image' => url('images/subcategories/' . $subCategory->image), // Return full URL here
            ]
        ], 200);
    }

    public function updateSubCategory($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateSubCategory = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);
        if($validateSubCategory->fails()){
            return response()->json($validateSubCategory->errors(), 401);
        }
        $subCategory = SubCategory::find($id);
        $imageName = $subCategory->image;
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/subcategories'), $imageName);
            $imageName = url('images/subcategories/' . $imageName);
        }
        if(!$subCategory){
            return response()->json(['message' => 'SubCategory not found'], 404);
        }
        $subCategory->update([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
            'image' => $imageName,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'SubCategory updated successfully',
           'subCategory' => [
                'id' => $subCategory->id,
                'name' => $subCategory->name,
                'category_id' => $subCategory->category->name,
                'image' => url('images/subcategories/' . $subCategory->image), // Return full URL here
            ]
        ], 200);
    }

    public function deleteSubCategory($id)
    {
        $subCategory = SubCategory::find($id);
        if(!$subCategory){
            return response()->json(['message' => 'SubCategory not found'], 404);
        }
        $subCategory->delete();
        return response()->json([
            'success' => true,
            'message' => 'SubCategory deleted successfully',
            'subCategory' => [
                'id' => $subCategory->id,
                'name' => $subCategory->name,
                'category_id' => $subCategory->category->name,
                'image' => url('images/subcategories/' . $subCategory->image), // Return full URL here
            ]
        ], 200);
    }
}
