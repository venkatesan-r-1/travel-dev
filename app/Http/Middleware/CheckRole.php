<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Redirect;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$role): Response
    {
        $actions = $request->route ()->getAction ();
        $roles=explode('|',$role);
        if($request->user()){
            if(request()->is('visa_reports') && request()->user()->hasReportAccess('VISA'))
                return $next($request);
            if( request()->is('report') && request()->user()->hasReportAccess('TRAVEL') )
                return $next($request);
            if($request->user()->has_any_role_code($roles))
                return $next($request);
            else
                return Redirect::to('/unauthorised');
        }
        else{
            return Redirect::to('/auth/login');
        }
    }
}
