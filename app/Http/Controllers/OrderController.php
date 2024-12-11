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
            'order_status' => 'required|in:pending,delivered,completed,cancelled,transit',
            'deliveryAddress_id' => 'nullable|exists:delivery_address,id',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1'
        ]);

        if ($validateOrder->fails()) {
            return response()->json($validateOrder->errors(), 401);
        }

        $totalAmount = 0; // Initialize total amount
        $orderItems = []; // Initialize order items

        foreach ($request->input('products') as $productData) {
            $stock = Stock::where('product_id', $productData['product_id'])->first();
            $product = Product::find($productData['product_id']);
            if ($stock->qty < $productData['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available for product ID ' . $productData['product_id']
                ], 400);
            }

            $stock->qty -= $productData['qty'];
            $product->qty -= $productData['qty'];
            $stock->save();
            $product->save();

            $totalPrice = $product->price * $productData['qty'];
            $totalAmount += $totalPrice; // Add to total amount

            $orderItems[] = [
                'product_id' => $productData['product_id'],
                'product_name' => $product->product_name ?? 'Product name not available',
                'qty' => $productData['qty'],
                // 'size' => $productData['size'],
                // 'metal' => $productData['metal'],
                'size' => !empty($productData['size']) ? $productData['size'] : '',
    'metal' => !empty($productData['metal']) ? $productData['metal'] : '',
                'price' => $product->price,
                'total_price' => $totalPrice,
                'discount' => $product->discount,
            ];
        }

        do {
            $invoiceNumber = mt_rand(10000000, 99999999);
        } while (Order::where('invoice_number', $invoiceNumber)->exists());

        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $totalAmount, // Use the calculated total amount
            'order_status' => $request->input('order_status'),
            'invoice_number' => $invoiceNumber,
            'deliveryAddress_id' => $request->input('deliveryAddress_id'),
            'discount' => $request->input('discount'),
        ]);

      foreach ($orderItems as $item) {
    // Apply conditions for size
    $size = !empty($item['size']) && in_array($item['size'], ['Small', 'Medium', 'Large']) 
        ? $item['size'] 
        : null;

    // Apply conditions for metal
    $metal = !empty($item['metal']) && in_array($item['metal'], ['Gold', 'Silver', 'Platinum']) 
        ? $item['metal'] 
        : '';

    // Attach product with validated size and metal
    $order->products()->attach($item['product_id'], [
        'qty' => $item['qty'],
        'size' => $size,
        'metal' => $metal,
    ]);
}
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'order' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'order_status' => $order->order_status,
                'invoice_number' => $order->invoice_number,
                'deliveryAddress_id' => $order->deliveryAddress_id,
                'discount' => $order->discount,
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

        if ($validateOrder->fails()) {
            return response()->json($validateOrder->errors(), 401);
        }

        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $totalAmount = 0;
        $orderItems = [];

        // First, calculate the total changes needed in stock
        foreach ($request->input('products') as $productData) {
            $stock = Stock::where('product_id', $productData['product_id'])->first();
            $product = Product::find($productData['product_id']);

            // Find existing order product
            $existingProduct = $order->products()->where('product_id', $productData['product_id'])->first();

            // Calculate quantity difference
            $qtyDifference = $productData['qty'] - ($existingProduct ? $existingProduct->pivot->qty : 0);

            // Check stock sufficiency
            if ($stock->qty < $qtyDifference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available for product ID ' . $productData['product_id']
                ], 400);
            }

            // Update stock
            $stock->qty -= $qtyDifference;
            $stock->save();

            // Prepare order items
            $totalPrice = $product->price * $productData['qty'];
            $totalAmount += $totalPrice;

            $orderItems[] = [
                'product_id' => $productData['product_id'],
                'product_name' => $product->product_name ?? 'Product name not available',
                'qty' => $productData['qty'],
                'size' => $productData['size'],
                'metal' => $productData['metal'],
                'price' => $product->price,
                'total_price' => $totalPrice
            ];
        }

        // Update the order
        $order->update([
            'customer_id' => $request->input('customer_id'),
            'order_date' => $request->input('order_date'),
            'total_amount' => $totalAmount, // Use calculated total amount
            'order_status' => $request->input('order_status'),
            'deliveryAddress_id' => $request->input('deliveryAddress_id'),
            'discount' => $request->input('discount'),
        ]);

        // Sync products with new quantities
        $productsToSync = [];
        foreach ($request->input('products') as $productData) {
            $productsToSync[$productData['product_id']] = ['qty' => $productData['qty'], 'size' => $productData['size'], 'metal' => $productData['metal']];
        }
        $order->products()->sync($productsToSync);

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully',
            'order' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
                'order_date' => $order->order_date,
                'total_amount' => $totalAmount,
                'order_status' => $order->order_status,
                'invoice_number' => $order->invoice_number,
                'deliveryAddress_id' => $order->deliveryAddress_id,
                'discount' => $order->discount,
                'order_items' => $orderItems
            ]
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
                    'price' => $product->price,
                    'size' => $product->pivot->size,
                    'metal' => $product->pivot->metal,
                ];
            });
        }
        // Transform the orders to include 'customer_name' as a top-level field
        $orders = $orders->map(function ($order) use ($orderItems) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_id' => $order->customer_id,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'invoice_number' => $order->invoice_number,
                'order_status' => $order->order_status,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
                'deleted_at' => $order->deleted_at,
                'deliveryAddress_id' => $order->deliveryAddress_id,
                'discount' => $order->discount,
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
        $order = Order::with(['customer:id,name,email,phone', 'deliveryAddress:id,address', 'products'])->find($id);
        $orderItems = [];
        if ($order) {
            $order->products()->each(function ($product) use (&$orderItems) {
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->product_name ?? '',
                    'qty' => $product->pivot->qty,
                    'price' => $product->price,
                    'size' => $product->pivot->size,
                    'metal' => $product->pivot->metal,
                ];
            });
            // dd($orderItems);
        }
        // dd($orderItems);
        return response()->json([
            'success' => true,
            'message' => 'Order fetched successfully',
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => $order->customer,
                'order_date' => $order->order_date,
                'total_amount' => $order->total_amount,
                'invoice_number' => $order->invoice_number,
                'order_status' => $order->order_status,
                'deliveryAddress' => $order->deliveryAddress,
                'order_items' => $orderItems,
                'discount' => $order->discount,
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
