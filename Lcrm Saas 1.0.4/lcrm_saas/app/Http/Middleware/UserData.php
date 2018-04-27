<?php

namespace App\Http\Middleware;

use App\Repositories\SettingsRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Closure;
use App\Helpers\InitialGenerators;

class UserData
{
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
        $userRepository = new UserRepositoryEloquent(app());
        if ($userRepository->check()) {
            (new InitialGenerators())->generateData();
        }
        else{
            view()->share('settings',(new SettingsRepositoryEloquent(app()))->getAll());
        }

        return $next($request);
    }
}
