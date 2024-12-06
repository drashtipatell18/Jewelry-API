<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Order_Product;
class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateOrder = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'order_date'  => 'required|date',
            'total_amount' => 'required|numeric',
            'order_status' => 'required|in:pending,delivered,completed,cancelled,transit',
            'deliveryAddress_id' => 'nullable|exists:delivery_address,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1'
        ]);

        if ($validateOrder->fails()) {
            return response()->json($validateOrder->errors(), 401);
        }

        // Initialize an array to hold product IDs
        $productIds = [];

        // Process each product in the order
        foreach ($request->input('products') as $productData) {
            $stock = Stock::find($productData['product_id']);
            $product = Product::find($productData['product_id']);
            // Check if stock quantity is sufficient
            if ($stock->qty < $productData['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available for product ID ' . $productData['product_id']
                ], 400);
            }

            // Deduct the ordered quantity from the stock
            $stock->qty -=  $productData['qty'];
            $product->qty -=  $productData['qty'];
            $stock->save();
            $product->save();
        }

          // Generate a unique 6-digit invoice number
        do {
            $invoiceNumber = mt_rand(10000000, 99999999);
        } while (Order::where('invoice_number', $invoiceNumber)->exists());

        // Create the order
        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'order_status' => $request->input('order_status'),
            'invoice_number' => $invoiceNumber,
            'deliveryAddress_id' => $request->input('deliveryAddress_id'),
        ]);

        // Create order items and get product name
        $orderItems = [];
        foreach ($request->input('products') as $productData) {
            $product = Product::find($productData['product_id']);
            $order->products()->attach($productData['product_id'], ['qty' => $productData['qty']]);
            $orderItems[] = [
                'product_id' => $productData['product_id'],
                'product_name' => $product->product_name ?? 'Product name not available',
                'qty' => $productData['qty'],
                'price' => $product->price
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'order' => [
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'order_status' => $order->order_status,
                'invoice_number' => $order->invoice_number,
                'deliveryAddress_id' => $order->deliveryAddress_id,
                'order_items' => $orderItems
            ],
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
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1'
        ]);
        if($validateOrder->fails()){
            return response()->json($validateOrder->errors(), 401);
        }
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Process each product in the order
        foreach ($request->input('products') as $productData) {
            // Fetch stock using the correct method
            $stock = Stock::where('product_id', $productData['product_id'])->first();
            $product = Product::find($productData['product_id']);
            if($productData['product_id'] == $product->id){
                // Check if stock quantity is sufficient
                $existingProduct = $order->products()->where('product_id', $productData['product_id'])->first();
                $qtyDifference = $productData['qty'] - ($existingProduct ? $existingProduct->pivot->qty : 0);
            }
            // Check if stock quantity is sufficient after considering the difference
            if ($stock->qty < $qtyDifference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available for product ID ' . $productData['product_id']
                ], 400);
            }

        // Update the stock quantity
        $stock->qty -= $qtyDifference;

        $stock->save();

            // Update the order product quantity
            $order->products()->syncWithoutDetaching([$productData['product_id'] => ['qty' => $productData['qty']]]);
        }

        // Update the order
        $order->update([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $request->input('total_amount'),
            'order_status' => $request->input('order_status'),
            'deliveryAddress_id' => $request->input('deliveryAddress_id'),
        ]);
        $orderItems = [];
        foreach ($request->input('products') as $productData) {
            $product = Product::find($productData['product_id']);
            $order->products()->attach($productData['product_id'], ['qty' => $productData['qty']]);
            $orderItems[] = [
                'product_id' => $productData['product_id'],
                'product_name' => $product->product_name ?? 'Product name not available',
                'qty' => $productData['qty'],
                'price' => $product->price
            ];
        }
        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'order' => $order
        ], 200);
    }


     public function getAllOrder()
    {
        // Fetch all orders with only the 'name' field from the customer and the entire deliveryAddress
        $orders = Order::with(['customer', 'deliveryAddress'])->get();
        $orderItems = [];
        foreach ($orders as $order) {
            $order->products()->each(function ($product) use (&$orderItems) {
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name ?? '',
                    'qty' => $product->pivot->qty,
                    'price' => $product->price
                ];
            });
        }
        // Transform the orders to include 'customer_name' as a top-level field
        $orders = $orders->map(function ($order) use ($orderItems) {
            return [
                'id' => $order->id,
                'customer_id' => $order->customer_id,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'invoice_number' => $order->invoice_number,
                'order_status' => $order->order_status,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'deleted_at' => $order->deleted_at,
                'deliveryAddress_id' => $order->deliveryAddress_id,
                'customer_name' => $order->customer ? $order->customer->name : null,
                'customer_email' => $order->customer ? $order->customer->email : null,
                'customer_phone' => $order->customer ? $order->customer->phone : null,
                'delivery_address' => $order->deliveryAddress ? $order->deliveryAddress->address : null,
                'order_items' => $orderItems
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Orders fetched successfully',
            'orders' => $orders
        ], 200);
    }


    public function getOrderById(Request $request,$id)
    {
        $order = Order::with(['customer', 'deliveryAddress', 'products'])->find($id);
        $orderItems = [];
        if ($order) {
            $order->products()->each(function ($product) use (&$orderItems) {
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name ?? '',
                    'qty' => $product->pivot->qty,
                    'price' => $product->price
                ];
            });
        }
        return response()->json([
            'success' => true,
            'message' => 'Order fetched successfully',
            'order' => [
                'id' => $order->id,
                'customer_id' => $order->customer_id,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'invoice_number' => $order->invoice_number,
                'order_status' => $order->order_status,
                'deliveryAddress_id' => $order->deliveryAddress_id,
                'order_items' => $orderItems
            ],
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

    public function updateOrderProduct(Request $request, $id)
    {
        if($request->user()->role_id !== 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $orderProduct = Order_Product::find($id);
        $orderProduct->update([
            'product_id' => $request->input('product_id'),
            'qty' => $request->input('qty'),
            'price' => $request->input('price')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Order product updated successfully',
            'orderProduct' => $orderProduct
        ], 200);
    }

    public function deleteOrderProduct($id)
    {
        $orderProduct = Order_Product::find($id);
        $orderProduct->delete();
        return response()->json([
            'success' => true,
            'message' => 'Order product deleted successfully',
            'orderProduct' => $orderProduct
        ], 200);
    }
}
