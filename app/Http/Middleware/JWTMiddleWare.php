<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleWare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // try{
        //     if(!$user = JWTAuth::parseToken()->authenticate()){
        //         return response()->json(['user_not_found'], 400);
        //     }
        // }catch (TokenExpiredException $e){
        //     return response()->json(['token_expired'=> $e->getMessage()], 401);
        // }catch (TokenInvalidException $e){
        //     return response()->json(['token_invalid'=> $e->getMessage()], 401);
        // }catch (JWTException $e){
        //     return response()->json(['token_absent'=> $e->getMessage()], 401);
        // }

        return $next($request);
    }
}
