<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->query('lang');
        $allowedLocales = ['en', 'ku', 'ar'];

        if ($lang && in_array($lang, $allowedLocales)) {

            App::setLocale($lang);
            Session::put('locale', $lang);
        } elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            $user = $request->user();
            if ($user && $user->locale) {
                App::setLocale($user->locale);
            }
        }

        return $next($request);
    }
}
