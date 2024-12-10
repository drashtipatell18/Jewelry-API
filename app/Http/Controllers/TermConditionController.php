<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TermCondition;
use Illuminate\Support\Facades\Validator;
class TermConditionController extends Controller
{
    public function createTermCondition(Request $request)
    {
        if($request->user()->role_id !== 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateTermCondition = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ]);
        if($validateTermCondition->fails()){
            return response()->json($validateTermCondition->errors(), 401);
        }
        $termCondition = TermCondition::create([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Term Condition created successfully',
            'data' => $termCondition
        ], 200);
    }

    public function getAllTermCondition()
    {
        $termCondition = TermCondition::all();
        return response()->json([
            'success' => true,
            'message' => 'Term Condition fetched successfully',
            'data' => $termCondition
        ], 200);
    }

    public function getTermConditionById($id)
    {
        $termCondition = TermCondition::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Term Condition fetched successfully',
            'data' => $termCondition
        ], 200);
    }

    public function updateTermCondition($id, Request $request)
    {
        if($request->user()->role_id !== 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateTermCondition = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required'
        ]);
        if($validateTermCondition->fails()){
            return response()->json($validateTermCondition->errors(), 401);
        }
        $termCondition = TermCondition::find($id);
        $termCondition->update([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Term Condition updated successfully',
            'data' => $termCondition
        ], 200);
    }

    public function deleteTermCondition($id, Request $request)
    {
        if($request->user()->role_id !== 1){
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $termCondition = TermCondition::find($id);
        $termCondition->delete();
        return response()->json([
            'success' => true,
            'message' => 'Term Condition deleted successfully'
        ], 200);
    }
}
