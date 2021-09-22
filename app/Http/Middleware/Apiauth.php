<?php

namespace App\Http\Middleware;
use App\User;
use Closure;

class Apiauth extends \App\Http\Controllers\Controller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(&$request, Closure $next)
    {
        if((new User())->CheckUserAuth($request)){
            return $next($request);
        }else{
           $message="You are not Authenticate , Please login ";
           return $this->apiResponse(['error'=>440,'message'=>$message],true);
        }
        
    }
}
