<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChangeLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        app()->setLocale('ar');
        session(['country_id' => 1]);
        if ($request->country_id) {
            session(['country_id' => $request->country_id]);
        }

        if (isset($request->lang)  && $request->lang == 'en')
            app()->setLocale('en');
        // Auth::guard()
        return $next($request);
    }
}
