<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\User;
use App\Models\Organization;
use App\Models\OrganizationRole;

/**
 * Interface OrgnizationRolesRepository.
 */
interface OrganizationRolesRepository extends RepositoryInterface
{
    public function attachRole(Organization $organization, User $use, OrganizationRole $role);

    public function getRole(Organization $organization, User $user);
}
