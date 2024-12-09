<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mail\ForgotPasswordMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
            'dob'  =>  'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'phone' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
            'image' => $filename,
            'phone' => $request->input('phone'),
            'dob' => $request->input('dob'),
            'gender'=>$request->input('gender')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'phone' => $user->phone,
                'dob' => $user->dob,
                'gender'=>$user->gender
            ]
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
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_id' => $user->role_id,
                    'phone' => $user->phone,
                    'dob' => $user->dob,
                    'gender'=>$user->gender
                ];
            })
        ], 200);
    }

    public function getUser($id)
    {
        $user = User::find($id);
        return response()->json([
            'success' => true,
            'message' => 'User Get successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'phone'=>$user->phone,
                'gender'=>$user->gender,
                'dob'=> $user->dob
            ]
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
            'dob' => 'required',
            'role_id' => 'required|exists:roles,id',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $request->input('role_id'),
            'phone' => $request->input('phone'),
            'dob' => $request->input('dob'),
            'gender'=>$request->input('gender')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Update successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'phone' => $user->phone,
                'dob' => $user->dob,
                'gender'=>$user->gender
            ]
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
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'phone' => $user->phone,
                'dob' => $user->dob,
                'gender'=>$user->gender
            ]
        ], 200);
    }

    public function updateProfile(Request $request, $id)
    {
        $validateUser = Validator::make($request->all(), [
            'name' => 'required',
            'dob' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required',
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
            'dob' => $request->input('dob'),
             'gender'=>$request->input('gender')
        ]);
        return response()->json([
            'success' => true,
            'message' => 'User Profile Update successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'phone' => $user->phone,
                'dob' => $user->dob,
                'gender'=>$user->gender
            ]
        ], 200);
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }

        $user = User::where('email', $request->email)->first();
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT); // Changed range to 0-9999 for 4-digit OTP
        $user->password_reset_otp = $otp;
        $user->password_reset_otp_expires_at = now()->addMinutes(15);
        $user->save();

        if ($otp) {

            Mail::to($user->email)->send(new ForgotPasswordMail($otp));

            return response()->json(['success' => true, 'message' => 'Password reset link sent successfully.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }
    }

    public function sendOTP(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'otp' => 'required|numeric|digits:4',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }
        $otp = $request->otp;
        $user = User::where('password_reset_otp', $otp)->first();
        if($user){
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.'
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.'
            ], 400);
        }
    }

    // Method to handle the form submission
    public function postReset($otp, Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation fails',
                'error' => $validateUser->errors()
            ], 401);
        }

        $user = User::where('password_reset_otp', $otp)->first();

        if ($user) {
            if (empty($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
            $user->remember_token = Str::random(40);
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['success' => true,'message' => 'Password successfully reset.'], 200);
        } else {
            return response()->json(['success' => false,'message' => 'Invalid token.'], 400);
        }
    }

    public function changePassword(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required|string|min:8|same:new_password',
        ]);
        if($validateUser->fails()){
            return response()->json($validateUser->errors(), 401);
        }

        $user = Auth::user();
        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated.'
            ], 401);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The current password is incorrect.'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ], 200);
    }
}
