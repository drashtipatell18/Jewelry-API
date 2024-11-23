<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token is missing.',
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);


         if (!$accessToken || !$accessToken->tokenable) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid token.',
            ], 401);
        }


        if (!Auth::loginUsingId($accessToken->tokenable_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. User not found.',
            ], 401);
        }


        return $next($request);

    }
}
