<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next,...$roles)
    {
       
        $user = Auth::user();
        // if the user is super admin authorize  on any request
        if($user->role == 'super_admin'){
            return $next($request);
        }
        // Ensure user is authenticated and has the required role(s)
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => __('messages.not_authorized')], 403);
        }
        
        return $next($request);

    }
}
