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

        $imageName = null;
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/subcategories'), $imageName);
        }
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
                'category_name' => $subCategory->category->name,
                 'category_id' => $subCategory->category->id,
                'image' => $imageName ? url('images/subcategories/' . $imageName) : null,
            ]
        ], 200);
    }

    public function getAllSubCategory()
    {
        $subCategories = SubCategory::with('category')->get();
        return response()->json([
            'success' => true,
            'message' => 'SubCategories fetched successfully',
            'subCategories' => $subCategories->map(function($subCategory) {
                return [
                    'id' => $subCategory->id,
                    'name' => $subCategory->name,
                    'category_name' => $subCategory->category->name,
                    'category_id' => $subCategory->category->id,
                    'image' => url('images/subcategories/' . $subCategory->image), // Return full URL here
                ];
            }),
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
               'category_name' => $subCategory->category->name,
                    'category_id' => $subCategory->category->id,
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
                'category_name' => $subCategory->category->name,
                    'category_id' => $subCategory->category->id,
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
                'category_name' => $subCategory->category->name,
                    'category_id' => $subCategory->category->id,
                'image' => url('images/subcategories/' . $subCategory->image), // Return full URL here
            ]
        ], 200);
    }

    public function AllDeleteSubCategory(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        SubCategory::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All SubCategories deleted successfully'
        ], 200);
    }

    public function updateStatusSubCategory($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $subCategory = SubCategory::find($id);

        if (!$subCategory) {
            return response()->json(['success' => false, 'message' => 'SubCategory not found'], 404);
        }

        $subCategory->update([
            'status' => $request->input('status')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SubCategory status updated successfully',
            'subCategory' => $subCategory
        ], 200);
    }

    public function getAllActiveSubCategory(Request $request)
    {
        $subCategories = SubCategory::where('status', 'active')->get();
        return response()->json([
            'success' => true,
            'message' => 'SubCategories Active fetched successfully',
            'subCategories' => $subCategories
        ], 200);
    }

    public function getAllInactiveSubCategory(Request $request)
    {
        $subCategories = SubCategory::where('status', 'inactive')->get();
        return response()->json([
            'success' => true,
            'message' => 'SubCategories Inactive fetched successfully',
            'subCategories' => $subCategories
        ], 200);
    }


}
