<?php

namespace App\Http\Middleware;
use Auth;
use Closure;

class School
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
        $role= $request->route()->getPrefix();
        if(!Auth::guard($role)->check()){
            return redirect('/'.$role);
        }
        return $next($request);

    }
}
