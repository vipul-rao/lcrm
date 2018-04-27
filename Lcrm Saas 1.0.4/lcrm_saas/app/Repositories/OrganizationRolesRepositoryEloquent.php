<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\OrganizationRole;
use App\Models\Organization;
use App\Models\User;

/**
 * Class OrgnizationRolesRepositoryEloquent.
 */
class OrganizationRolesRepositoryEloquent extends BaseRepository implements OrganizationRolesRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return OrganizationRole::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function attachRole(Organization $organization, User $user, OrganizationRole $role)
    {
        return $user->organizations()->syncWithoutDetaching([$organization->id => ['role_id' => $role->id]]);
    }

    public function getRole(Organization $organization, User $user)
    {
        return $this->find($user->organizations()->first()->pivot->role_id)->slug;
    }
}
