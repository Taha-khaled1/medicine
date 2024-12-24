<?php

namespace App\Http\Middleware;

use App\Traits\Api\AuthTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthenticateSanctum
{
    use AuthTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['message' =>  __('custom.token_not_available')], 401);
        }

        $token = $request->bearerToken();


        if (empty($token)) {
            return response()->json(['message' =>  __('custom.token_not_available')], 401);
        }
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => __('custom.login_first')], 401);
        }
        if ($user->status == '0') {
            // $request->user->tokens()->delete();
            // $this->updateFcm($request->user, '-');
            return response()->json(['message' => __('custom.user_blocked'), 'user' => $user], 501);
        }
        $request->merge(['user' => $user]);

        return $next($request);
    }
}
