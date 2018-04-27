<?php

namespace App\Http\Controllers;

use App\Repositories\OrganizationRolesRepositoryEloquent;
use App\Repositories\UserRepositoryEloquent;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $userRepository;

    private $organizationRolesRepository;

    public function __construct()
    {
    }

    public function getParams()
    {
        $this->userRepository = new UserRepositoryEloquent(app());

        $this->organizationRolesRepository = new OrganizationRolesRepositoryEloquent(app());

    }

    public function getUser()
    {
        $this->getParams();
        $user = $this->userRepository->getWithCustomer();
        $user->orgRole = $this->organizationRolesRepository->getRole($this->userRepository->getOrganization(), $user);

        return $user;
    }
}
