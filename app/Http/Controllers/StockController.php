<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Stock;
use Carbon\Carbon;

class StockController extends Controller
{
    public function createStock(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'product_id' => 'required|exists:products,id',
            'date' => 'required|date',
            'status' => 'required|in:in-stock,out-stock,low-stock',
            'qty' => 'required|integer|min:0',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $stock = Stock::create([
            'category_id' => $request->input('category_id'),
            'sub_category_id' => $request->input('sub_category_id'),
            'product_id' => $request->input('product_id'),
            'date' => Carbon::parse($request->input('date'))->format('Y-m-d'),
            'status' => $request->input('status'),
            'qty' => $request->input('qty'),
        ]);
        return response()->json(
            [
                'success' => true,
                'message' => 'Stock created successfully',
                'data' => $stock
            ], 201);
    }

    public function getAllStocks()
    {
        $stocks = Stock::all();
        return response()->json([
            'success' => true,
            'message' => 'Stocks fetched successfully',
            'data' => $stocks
        ], 200);
    }

    public function getStockById($id)
    {
        $stock = Stock::find($id);
        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Stock fetched successfully',
            'data' => $stock
        ], 200);
    }

    public function updateStock($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'product_id' => 'required|exists:products,id',
            'date' => 'required|date',
            'status' => 'required',
            'qty' => 'required|integer|min:0',
        ]);
        $stock = Stock::find($id);
        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }
        $stock->update($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully',
            'data' => $stock
        ], 200);
    }

    public function deleteStock($id)
    {
        $stock = Stock::find($id);
        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }
        $stock->delete();
        return response()->json([
            'success' => true,
            'message' => 'Stock deleted successfully'
        ], 200);
    }

    public function updateStatusStock($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:in-stock,out-stock,low-stock',
            'qty' => 'required|integer|min:0',
            'date' => 'required|date',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        $stock = Stock::find($id);
        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => 'Stock not found'
            ], 404);
        }
        $stock->update([
            'status' => $request->input('status'),
            'qty' => $request->input('qty'),
            'date' => Carbon::parse($request->input('date'))->format('Y-m-d'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Stock status updated successfully',
            'data' => $stock
        ], 200);
    }

    public function filterStock(Request $request)
    {
        $query = Stock::query();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by date if provided
        if ($request->has('date')) {
            $query->whereDate('date', $request->input('date'));
        }

        $stocks = $query->get();
        return response()->json([
            'success' => true,
            'message' => 'Stocks fetched successfully',
            'data' => $stocks
        ], 200);
    }

    public function getOutStock()
    {
        $stocks = Stock::where('status', 'out-stock')->get();
        return response()->json([
            'success' => true,
            'message' => 'Stocks fetched successfully',
            'data' => $stocks
        ], 200);
    }

    public function getInStock()
    {
        $stocks = Stock::where('status', 'in-stock')->get();
        return response()->json([
            'success' => true,
            'message' => 'Stocks fetched successfully',
            'data' => $stocks
        ], 200);
    }

    public function getLowStock()
    {
        $stocks = Stock::where('qty', '<', 10)->orWhere('status', 'low-stock')->get();
        return response()->json([
            'success' => true,
            'message' => 'Stocks fetched successfully',
            'data' => $stocks
        ], 200);
    }
}
