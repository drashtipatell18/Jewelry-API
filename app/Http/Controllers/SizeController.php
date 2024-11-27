<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Size;

class SizeController extends Controller
{
    public function createSize(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateSize = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);
        if($validateSize->fails()){
            return response()->json($validateSize->errors(), 401);
        }

        $size = Size::create([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Size created successfully',
            'size' => $size
        ], 201);
    }

    public function getAllSizes()
    {
        $sizes = Size::all();
        return response()->json([
            'success' => true,
            'message' => 'Sizes fetched successfully',
            'sizes' => $sizes
        ], 200);
    }

    public function getSizeById($id)
    {
        $size = Size::find($id);
        return response()->json($size);
    }

    public function updateSize(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateSize = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
        ]);
        if($validateSize->fails()){
            return response()->json($validateSize->errors(), 401);
        }
        $size = Size::find($id);
        $size->update([
            'name' => $request->input('name'),
            'category_id' => $request->input('category_id'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Size updated successfully',
            'size' => $size
        ], 200);
    }

    public function deleteSize($id)
    {
        $size = Size::find($id);
        $size->delete();
        return response()->json([
            'success' => true,
            'message' => 'Size deleted successfully',
            'size' => $size 
        ], 200);
    }
}
