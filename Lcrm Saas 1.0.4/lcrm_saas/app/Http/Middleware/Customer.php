<?php

namespace App\Http\Middleware;

use App\Repositories\OrganizationRolesRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Closure;

class Customer
{
    private $userRepository;
    private $organizationRolesRepository;

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
        $this->userRepository = new UserRepositoryEloquent(app());

        $this->organizationRolesRepository = new OrganizationRolesRepositoryEloquent(app());

        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();
        $role = $this->organizationRolesRepository->getRole($organization, $user);
        if ('customer' == $role) {
            return $next($request);
        }
    }
}
