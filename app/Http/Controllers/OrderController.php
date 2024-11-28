<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateOrder = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'order_date'  => 'required',
            'total_amount' => 'required',
            'order_status' => 'required'
        ]);
        if($validateOrder->fails()){
            return response()->json($validateOrder->errors(), 401);
        }
        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'order_status' => $request->input('order_status')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'category' => $order
        ], 200);
    }

    public function getAllOrder()
    {
        $orders = Order::all();
        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully',
            'sizes' => $orders
        ], 200);
    }

    public function getOrderById($id)
    {
        $order = Order::find($id);
        return response()->json($order);
    }

    public function updateOrder(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateOrder = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'order_date'  => 'required',
            'total_amount' => 'required',
            'order_status' => 'required'
        ]);
        if($validateOrder->fails()){
            return response()->json($validateOrder->errors(), 401);
        }
        $order = Order::find($id);
        $order->update([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'order_status' => $request->input('order_status'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'size' => $order
        ], 200);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
            'size' => $order 
        ], 200);
    }
}
