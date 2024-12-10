<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DeliveryAddress;

class DeliveryAddressController extends Controller
{
    public function createDeliveryAddress(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateAddress = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'address'  => 'required',
            'pincode'  => 'required',
            'city'  => 'required',
            'state'  => 'required',
            'type'  => 'required',
            'status'   => 'nullable'
        ]);
        if($validateAddress->fails()){
            return response()->json($validateAddress->errors(), 401);
        }

        $deliveryAddress = DeliveryAddress::create([
            'customer_id' => $request->input('customer_id'),
            'address' => $request->input('address'),
            'pincode' => $request->input('pincode'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'type' => $request->input('type'),
            'status' => $request->input('status')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Delivery Address created successfully',
            'deliveryAddres' => $deliveryAddress
        ], 200);
    }
    public function getAllDeliveryAddress()
    {
        $deliveryAddress = DeliveryAddress::all();
        return response()->json([
            'success' => true,
            'message' => 'Deleivery Address fetched successfully',
            'deliveryAddress' => $deliveryAddress
        ], 200);
    }

    public function getDeliveryAddressById(Request $request,$id)
    {
        $deliveryAddress = DeliveryAddress::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Deleivery Address fetched successfully',
            'deliveryAddres' => $deliveryAddress,
        ], 200);
    }
    public function updateDeliveryAddress(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateAddress = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,id',
            'address'  => 'required',
            'pincode'  => 'required',
            'city'  => 'required',
            'state'  => 'required',
            'type'  => 'required',
            'status'   => 'nullable'
        ]);
        if($validateAddress->fails()){
            return response()->json($validateAddress->errors(), 401);
        }
        $deliveryAddress = DeliveryAddress::find($id);
        $deliveryAddress->update([
            'customer_id' => $request->input('customer_id'),
            'address' => $request->input('address'),
            'pincode' => $request->input('pincode'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'type' => $request->input('type'),
            'status' => $request->input('status')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Delivery Address updated successfully',
            'deliveryAddres' => $deliveryAddress
        ], 200);
    }

    public function deleteDeliveryAddress($id)
    {
        $deliveryAddress = DeliveryAddress::find($id);
        $deliveryAddress->delete();
        return response()->json([
            'success' => true,
            'message' => 'Delivery Address deleted successfully',
            'deliveryAddres' => $deliveryAddress
        ], 200);
    }
}
