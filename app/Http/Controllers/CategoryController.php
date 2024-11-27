<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    public function createCategory(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateCategory = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if($validateCategory->fails()){
            return response()->json($validateCategory->errors(), 401);
        }
        $category = Category::create([
            'name' => $request->input('name')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ], 200);
    }

    public function getAllCategory(Request $request)
    {
        $categories = Category::all();
        return response()->json([
            'success' => true,
            'message' => 'Categories fetched successfully',
            'categories' => $categories
        ], 200);
    }

    public function getCategory($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Category fetched successfully',
            'category' => $category
        ], 200);
    }

    public function updateCategory($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        $category->update([
            'name' => $request->input('name')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => $category
        ], 200);
    }

    public function deleteCategory($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
            'category' => $category
        ], 200);
    }

}
