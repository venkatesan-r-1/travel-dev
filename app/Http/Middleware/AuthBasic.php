<?php

namespace App\Http\Middleware;

use Closure;

class AuthBasic
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
        if(!($request->getUser() == "PAYMENTSTEST" && $request->getPassword() == "PAYMENTSTEST@123")){
            return response()->json(['message'=>'Auth Failed.'],401);
        }else{
            return $next($request);
        }
    }
}
