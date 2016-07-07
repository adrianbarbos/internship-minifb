<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!User::whereApiToken($request->bearerToken())->first()) {
            return response([
                'message' => 'Unauthorized'
            ], 403);
        } 

        return $next($request);
    }
}
