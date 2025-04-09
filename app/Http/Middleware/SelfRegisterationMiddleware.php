<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class SelfRegisterationMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
            $selfRegisteration = DB::table('global_settings')->value('self_registeration');

            if (!$selfRegisteration) {
                return response()->json(['error' => __('messages.self_register')], 404);
            }

        return $next($request);
    }
}
