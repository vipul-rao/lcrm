<?php

namespace App\Http\Middleware;

use Closure;
use Settings;

class AvailableModules
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module)
    {
        //Check if the given modules is enable then only proceed
        if (in_array($module, Settings::get('modules'))) {
            return $next($request);
        }

        return redirect()->back()->withErrors(['message' => 'Permission denied']);
    }
}
