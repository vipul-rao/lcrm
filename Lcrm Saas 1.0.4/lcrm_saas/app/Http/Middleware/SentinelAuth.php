<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class SentinelAuth
{
    /**
     * Sentinel - Check login status
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Sentinel::check()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('/');
            }
        }
        return $next($request);
    }
}
