<?php

namespace App\Http\Middleware;

use Closure;
use Dingo\Api\Routing\Helpers;
use JWTAuth;

class ApiCustomer
{
    use Helpers;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->inRole('customer')) {
            return $next($request);
        }

        return response()->json(['error' => 'could_not_access'], 500);
    }
}
