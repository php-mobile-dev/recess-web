<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyReferrerMiddleware
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
        if($request->hasHeader('app-key') && $request->header('app-key') == env('APP_HEADER_KEY')){
            return $next($request);
        }else{
            return response()->json(['error' => 'Not a valid request. Please provide app key'], 401);
        }
    }
}