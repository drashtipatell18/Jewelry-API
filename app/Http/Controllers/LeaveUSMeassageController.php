<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveUSMeassage;
use Illuminate\Support\Facades\Validator;
class LeaveUSMeassageController extends Controller
{
    public function createLeaveUSMeassage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        }
        $leaveUSMeassage = LeaveUSMeassage::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message')
        ]);
        return response()->json([
            'status' => true,
            'message' => 'Leave US Meassage created successfully',
            'data' => $leaveUSMeassage
        ], 200);
    }
}
