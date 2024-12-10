<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnOrder;
use Illuminate\Support\Facades\Validator;
use App\Models\Stock;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;
class ReturnOrderController extends Controller
{
    public function createReturnOrder(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateReturnOrder = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:users,id',
            'stock_id' => 'required|exists:stocks,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required',
            'return_status' => 'required|in:pending,accepted,rejected',
            'price' => 'required'
        ]);
        if($validateReturnOrder->fails()){
            return response()->json($validateReturnOrder->errors(), 401);
        }

        $customer = User::find($request->input('customer_id'));
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        $providedOtp = $request->input('otp');
        $expectedOtp = "123456";

        if ($providedOtp !== $expectedOtp) {
            return response()->json(['message' => 'OTP is incorrect'], 400);
        }

        $returnOrder = ReturnOrder::create([
            'order_id' => $request->input('order_id'),
            'customer_id' => $request->input('customer_id'),
            'stock_id' => $request->input('stock_id'),
            'product_id' => $request->input('product_id'),
            'return_date' => $request->input('return_date'),
            'return_status' => 'pending',
            'price' => $request->input('price'),
            'reason'=>$request->input('reason'),
            'otp' => $providedOtp
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Return Order created successfully',
            'returnOrder' => $returnOrder
        ], 200);

    }

    public function getAllReturnOrder()
    {
       
        $returnOrders = ReturnOrder::with([
    'order' => function ($query) {
        $query->withTrashed(); // Include soft-deleted orders
    },
    'customer' => function ($query) {
        $query->withTrashed(); // Include soft-deleted customers
    },
    'stock' => function ($query) {
        $query->withTrashed(); // Include soft-deleted stock
    },
    'product' => function ($query) {
        $query->withTrashed(); // Include soft-deleted products
    },
])->get();
        $formattedReturnOrders = $returnOrders->map(function($returnOrder) {
            return [
                'id'=>$returnOrder->id,
                'order' => isset($returnOrder->order->id) ? $returnOrder->order->id : null,
                'customer' => isset($returnOrder->customer->name) ? $returnOrder->customer->name : null,
                'stock' => isset($returnOrder->stock->id) ? $returnOrder->stock->id : null,
                'product' => isset($returnOrder->product->product_name) ? $returnOrder->product->product_name : null,
                'return_date' => $returnOrder->return_date,
                'return_status' => $returnOrder->return_status,
                'price' => $returnOrder->price,
                'reason'=>$returnOrder->reason
                
            ];
        });
        return response()->json([
            'success' => true,
            'returnOrders' => $formattedReturnOrders
        ], 200);
    }

    public function getReturnOrderById($id)
    {
        $returnOrder = ReturnOrder::with('order','customer','stock','product')->find($id);
        return response()->json([
            'success' => true,
            'returnOrder' => [
                'order' => isset($returnOrder->order->id) ? $returnOrder->order->id : null,
                'customer' => isset($returnOrder->customer->name) ? $returnOrder->customer->name : null,
                'stock' => isset($returnOrder->stock->id) ? $returnOrder->stock->id : null,
                'product' => isset($returnOrder->product->product_name) ? $returnOrder->product->product_name : null,
                'return_date' => $returnOrder->return_date,
                'return_status' => $returnOrder->return_status,
                'price' => $returnOrder->price,
                   'reason'=>$returnOrder->reason
                ]
        ], 200);
    }

    public function updateReturnOrder($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateReturnOrder = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:users,id',
            'stock_id' => 'required|exists:stocks,id',
            'product_id' => 'required|exists:products,id',
            'return_date' => 'required',
            'return_status' => 'required|in:pending,accepted,rejected',
            'price' => 'required'
        ]);
        if($validateReturnOrder->fails()){
            return response()->json($validateReturnOrder->errors(), 401);
        }
        $returnOrder = ReturnOrder::find($id);
        $returnOrder->update([
            'order_id' => $request->input('order_id'),
            'customer_id' => $request->input('customer_id'),
            'stock_id' => $request->input('stock_id'),
            'product_id' => $request->input('product_id'),
            'return_date' => $request->input('return_date'),
            'return_status' => $request->input('return_status'),
            'price' => $request->input('price'),
            'reason'=>$request->input('reason')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Return Order updated successfully',
            'returnOrder' => $returnOrder
        ], 200);
    }

    public function deleteReturnOrder($id)
    {
        $returnOrder = ReturnOrder::find($id);
        $returnOrder->delete();
        return response()->json([
            'success' => true,
            'message' => 'Return Order deleted successfully',
            'returnOrder' => $returnOrder
        ], 200);
    }

    public function AllDeleteReturnOrder(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        ReturnOrder::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All Return Order deleted successfully'
        ], 200);
    }

    public function updateStatusReturnOrder($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateReturnOrder = Validator::make($request->all(), [
            'return_status' => 'required|in:pending,accepted,rejected'
        ]);
        if($validateReturnOrder->fails()){
            return response()->json($validateReturnOrder->errors(), 401);
        }
        $returnOrder = ReturnOrder::find($id);

        // Check if the return status is accepted
        if ($request->input('return_status') === 'accepted') {
            // Add stock quantity back to the stock table
            $order = Order::find($returnOrder->order_id);

            $stock = Stock::find($returnOrder->stock_id);
            if ($stock) {
                $stock->qty += $order->qty; // Assuming 'quantity' is a field in ReturnOrder
                $stock->save();
            }

            $order->update([
                'order_status' => 'cancelled'
            ]);
        }

        $returnOrder->update([
            'return_status' => $request->input('return_status')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Return Order status updated successfully',
            'returnOrder' => $returnOrder
        ], 200);
    }

    public function getAllAcceptedReturnOrder()
    {
        $returnOrders = ReturnOrder::where('return_status', 'accepted')->get();
        return response()->json([
            'success' => true,
            'message' => 'All Accepted Return Order',
            'returnOrders' => $returnOrders
        ], 200);
    }

    public function getAllRejectedReturnOrder()
    {
        $returnOrders = ReturnOrder::where('return_status', 'rejected')->get();
        return response()->json([
            'success' => true,
            'message' => 'All Rejected Return Order',
            'returnOrders' => $returnOrders
        ], 200);
    }
}
