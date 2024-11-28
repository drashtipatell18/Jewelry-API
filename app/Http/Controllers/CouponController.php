<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class CouponController extends Controller
{
    public function createCoupon(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateCoupon = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code',
            'name' => 'required',
            'description' => 'nullable',
            'type' => 'required',
            'price' => 'required',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
        ]);
        if($validateCoupon->fails()){
            return response()->json($validateCoupon->errors(), 401);
        }
        $coupon = Coupon::create([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'price' => $request->input('price'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully',
            'coupon' => $coupon
        ], 200);
    }

    public function getAllCoupons()
    {
        $coupons = Coupon::all();
        return response()->json([
            'success' => true,
            'coupons' => $coupons
        ], 200);
    }

    public function getCouponById($id)
    {
        $coupon = Coupon::find($id);
        if(!$coupon){
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'coupon' => $coupon
        ], 200);
    }

    public function updateCoupon($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateCoupon = Validator::make($request->all(), [
            'code' => 'required|unique:coupons,code,'.$id,
            'name' => 'required',
            'description' => 'nullable',
            'type' => 'required',
            'price' => 'required',
            'start_date' => 'nullable',
            'end_date' => 'nullable',
        ]);
        if($validateCoupon->fails()){
            return response()->json($validateCoupon->errors(), 401);
        }
        $coupon = Coupon::find($id);
        $coupon->update([
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'type' => $request->input('type'),
            'price' => $request->input('price'),
            'start_date' => Carbon::parse($request->input('start_date'))->format('Y-m-d'),
            'end_date' => Carbon::parse($request->input('end_date'))->format('Y-m-d'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully',
            'coupon' => $coupon
        ], 200);

    }

    public function deleteCoupon($id)
    {
        $coupon = Coupon::find($id);
        if(!$coupon){
            return response()->json([
                'success' => false,
                'message' => 'Coupon not found'
            ], 404);
        }
        $coupon->delete();
        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully',
            'coupon' => $coupon
        ], 200);
    }
}
