<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function createUser(Request $request)
    {
        // Check if the authenticated user has admin role (role_id = 1)
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validateUser = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }
        $filename = '';
        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $filename);
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
            'image' => $filename,
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => $user
        ], 200);
    }

    public function getAllUser(Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => 'Users Get All successfully',
            'users' => $users
        ], 200);
    }

    public function getUser($id)
    {
        $user = User::find($id);
        return response()->json([
            'success' => true,
            'message' => 'User Get successfully',
            'user' => $user
        ], 200);
    }

    public function updateUser($id, Request $request)
    {
        if ($request->user()->role_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $validateUser = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $filename = $user->image;
        $image = $request->file('image');
        if($image){
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $filename);
        }
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
            'image' => $filename,
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Update successfully',
            'user' => $user
        ], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User Delete successfully',
            'user' => $user
        ], 200);
    }
}
