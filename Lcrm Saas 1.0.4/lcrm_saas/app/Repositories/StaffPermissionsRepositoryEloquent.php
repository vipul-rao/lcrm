<?php

namespace App\Repositories;

use App\Models\OrganizationSetting;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class StaffPermissionsRepositoryEloquent extends BaseRepository implements StaffPermissionsRepository
{
    private $organization;

    private $userRepository;

    private $organizationRepository;

    public function getOrganization()
    {


        $this->userRepository = new UserRepositoryEloquent(app());

        $this->organizationRepository = new OrganizationRepositoryEloquent(app());

        $this->organization = $this->userRepository->getOrganization();
    }

    /**
     * Specify Model class name.
     */
    public function model()
    {
        return OrganizationSetting::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
