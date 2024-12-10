<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ReasonForCancellation;

class ReasonCancellationController extends Controller
{
    public function createReasonCancellation(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateReasonCancellation = Validator::make($request->all(), [
            'name'  => 'required',
        ]);
        if($validateReasonCancellation->fails()){
            return response()->json($validateReasonCancellation->errors(), 401);
        }

        $reasonCancellation = ReasonForCancellation::create([
            'name' => $request->input('name'),
            'status'=>'active'
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Reason For Cancellation created successfully',
            'reasonCancellation' => $reasonCancellation
        ], 200);
    }

    public function getAllReasonCancellation()
    {
        $reasonCancellation = ReasonForCancellation::all();
        return response()->json([
            'success' => true,
            'message' => 'Reason Cancellation fetched successfully',
            'reasonCancellation' => $reasonCancellation
        ], 200);
    }

    public function getReasonCancellationById(Request $request,$id)
    {
        $reasonCancellation = ReasonForCancellation::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Reason Cancellation fetched successfully',
            'reasonCancellation' => $reasonCancellation,
        ], 200);
    }

    public function updateReasonCancellation(Request $request, $id)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateReasonCancellation = Validator::make($request->all(), [
            'name'  => 'required',
        ]);
        if($validateReasonCancellation->fails()){
            return response()->json($validateReasonCancellation->errors(), 401);
        }
        $reasonCancellation = ReasonForCancellation::find($id);
        $reasonCancellation->update([
            'name' => $request->input('name'),
            'stutus'=>$request->input('status')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Reason Cancellation updated successfully',
            'reasonCancellation' => $reasonCancellation
        ], 200);
    }

    public function deleteReasonCancellation($id)
    {
        $reasonCancellation = ReasonForCancellation::find($id);
        $reasonCancellation->delete();
        return response()->json([
            'success' => true,
            'message' => 'Reason Cancellation deleted successfully',
            'deliveryAddres' => $reasonCancellation
        ], 200);
    }
    public function updateStatusReasonCancellation($id, Request $request)
    {
        $reasonCancellation = ReasonForCancellation::find($id);
        if(!$reasonCancellation){
            return response()->json(['success' => false, 'message' => 'Reason for Cancellation not found'], 404);
        }
        $reasonCancellation->update(['status' => $request->input('status')]);
        return response()->json([
            'success' => true,
            'message' => 'Reason for Cancellation status updated successfully',
            'data'=>$reasonCancellation
        ], 200);
    }
    public function AllDeleteReasonCancellation(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        ReasonForCancellation::query()->delete();
        return response()->json([
            'success' => true,
            'message' => 'All reason for cancellation  deleted successfully'
        ], 200);
    }
public function activeReasonForCancellation()
    {
        $reasonCancellation = ReasonForCancellation::where('status', 'active')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Active reason for cancellation retrieved successfully',
            'data' => $reasonCancellation,
        ], 200);
    }

}
