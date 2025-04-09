<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            // Get the token payload first
            $payload = JWTAuth::parseToken()->getPayload();
            // Check if the token type is 'access'
            if ($payload->get('type') !== 'access') {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }
            // If a guard is specified, verify it matches the token
            if ($guard && $payload->get('guard') !== $guard) {
                return response()->json(['error' => __('messages.token_invalid')], 401);
            }
            // Authenticate using the appropriate guard
            if ($guard) {
                $user = auth()->guard($guard)->authenticate();
            } else {
                $user = JWTAuth::parseToken()->authenticate();
            }
            if($user->status == 'inactive'){
                JWTAuth::invalidate(JWTAuth::getToken());
                return response()->json(['error' => __('messages.user_susbend')], 404);

            }

            if (!$user) {
                return response()->json(['error' => __('messages.user_not_found')], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => __('messages.token_invalid')], 401);
        }

        return $next($request);
    }
}
