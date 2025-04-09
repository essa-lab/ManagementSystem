<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;

class VerifiedMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            $user = auth()->guard('patron')->user();
            if (!$user) {
                return response()->json(['error' => __('messages.user_not_found')], 404);
            }
            if(!$user->verified){
                return response()->json(['error' => __('messages.not_verified')], 422);
            }
            
        } catch (JWTException $e) {
            return response()->json(['error' => __('messages.token_invalid')], 401);
        }

        return $next($request);
    }
}
