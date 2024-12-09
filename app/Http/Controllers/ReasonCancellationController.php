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


}
