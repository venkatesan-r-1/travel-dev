<?php

namespace App\Http\Middleware;
use App\Traits\EncryptionTrait;

use Closure;

class CheckKey
{
    use EncryptionTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $key = $this->get_new_key();
        if(!$key){
            return redirect('/key_not_found');
        }
        return $next($request);
    }
}