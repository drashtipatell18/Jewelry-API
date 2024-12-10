<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;
use Illuminate\Support\Facades\Validator;

class PrivacyPolicyController extends Controller
{
    public function createPrivacyPolicy(Request $request)
    {
        $validatePrivacyPolicy = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if($validatePrivacyPolicy->fails()){
            return response()->json($validatePrivacyPolicy->errors(), 401);
        }
        $privacyPolicy = PrivacyPolicy::create([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy created successfully',
            'data' => $privacyPolicy
        ], 200);
    }

    public function getAllPrivacyPolicy()
    {
        $privacyPolicy = PrivacyPolicy::all();
        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy fetched successfully',
            'data' => $privacyPolicy
        ], 200);
    }

    public function getPrivacyPolicyById($id)
    {
        $privacyPolicy = PrivacyPolicy::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy fetched successfully',
            'data' => $privacyPolicy
        ], 200);
    }

    public function updatePrivacyPolicy($id, Request $request)
    {
        $validatePrivacyPolicy = Validator::make($request->all(), [
            'title' => 'required',
        ]);
        if($validatePrivacyPolicy->fails()){
            return response()->json($validatePrivacyPolicy->errors(), 401);
        }
        $privacyPolicy = PrivacyPolicy::find($id);
        $privacyPolicy->update([
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy updated successfully',
            'data' => $privacyPolicy
        ], 200);
    }

    public function deletePrivacyPolicy($id)
    {
        $privacyPolicy = PrivacyPolicy::find($id);
        $privacyPolicy->delete();
        return response()->json([
            'success' => true,
            'message' => 'Privacy Policy deleted successfully'
        ], 200);
    }

    

}

