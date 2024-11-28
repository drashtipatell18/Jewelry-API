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

          // Generate a unique 6-digit invoice number
        do {
            $invoiceNumber = mt_rand(10000000, 99999999);
        } while (Order::where('invoice_number', $invoiceNumber)->exists());
        
        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'order_status' => $request->input('order_status'),
            'invoice_number' => $invoiceNumber,
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
            'orders' => $orders
        ], 200);
    }

    public function getOrderById(Request $request,$id)
    {
        // $order = Order::find($id);
        $order = Order::with('customer')->find($id);
        return response()->json([
            'success' => true,
            'message' => 'Order fetched successfully',
            'order' => $order,
        ], 200);
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
            'order' => $order
        ], 200);
    }

    public function deleteOrder($id)
    {
        $order = Order::find($id);
        $order->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
            'order' => $order 
        ], 200);
    }
}
