<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Stock;

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
            'order_status' => 'required|in:pending,delivered,completed,cancelled,transit',
            'deliveryAddress_id' => 'nullable',
            'stock_id' => 'required|exists:stocks,id',
            'qty' => 'required'
        ]);
        if($validateOrder->fails()){
            return response()->json($validateOrder->errors(), 401);
        }

        $stock = Stock::find($request->input('stock_id'));

          // Check if stock quantity is sufficient
        if ($stock->qty < $request->input('qty')) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

         // Deduct the ordered quantity from the stock
        $stock->qty -= $request->input('qty');
        $stock->save();

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
            'deliveryAddress_id' => $request->input('deliveryAddress_id'),
            'stock_id' =>$request->input('stock_id'),
            'qty' => $request->input('qty')
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
        $order = Order::with('customer','deliveryAddress')->find($id);
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
            'order_status' => 'required|in:pending,delivered,completed,cancelled,transit',
            'deliveryAddress_id' => 'nullable',
            'stock_id' => 'required|exists:stocks,id',
            'qty' => 'required'
        ]);
        if($validateOrder->fails()){
            return response()->json($validateOrder->errors(), 401);
        }
        $order = Order::find($id);

        // Check if stock quantity is sufficient
        $stock = Stock::find($request->input('stock_id'));

        // Calculate the difference in quantity
        $qtyDifference = $request->input('qty') - $order->qty;

        // Check if stock quantity is sufficient after considering the difference
        if ($stock->qty < $qtyDifference) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available'
            ], 400);
        }

        // Update the stock quantity
        $stock->qty -= $qtyDifference;
        $stock->save();

        // Update the order
        $order->update([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'order_status' => $request->input('order_status'),
            'deliveryAddress_id' => $request->input('deliveryAddress_id'),
            'stock_id' => $request->input('stock_id'),
            'qty' => $request->input('qty')
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

    public function AllDeleteOrder(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        Order::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All Orders deleted successfully'
        ], 200);
    }

    public function updateStatusOrder(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateOrder = Validator::make($request->all(), [
            'order_status' => 'required|in:pending,delivered,completed,cancelled,transit'
        ]);
        if($validateOrder->fails()){
            return response()->json($validateOrder->errors(), 401);
        }
        $order = Order::find($id);
        $order->update([
            'order_status' => $request->input('order_status')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => $order
        ], 200);
    }

    public function invoiceOrder($id)
    {
        $order = Order::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Invoice fetched successfully',
            'order' => $order
        ], 200);
    }
}
