<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainCategory;

class MainCategoryController extends Controller
{
    public function index()
    {
        $categories = MainCategory::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $category = MainCategory::create([
            'name' => $validated['name']
        ]);

        return response()->json($category, 200);
    }
    public function edit($id)
    {
        $category = MainCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        // Find the category by ID
        $category = MainCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Validate the request data
        $validated = $request->validate([
            'name' => 'required',
        ]);

        // Update the category
        $category->name = $validated['name'];
        $category->save();

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = MainCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Soft delete the category
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

}
