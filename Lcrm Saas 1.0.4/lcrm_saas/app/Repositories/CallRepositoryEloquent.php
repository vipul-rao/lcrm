<?php

namespace App\Repositories;

use App\Models\Call;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;


class CallRepositoryEloquent extends BaseRepository implements CallRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     */
    public function model()
    {
        return Call::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams(){


        $this->userRepository = new UserRepositoryEloquent(app());
    }

    public function getAll()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('calls.responsible','calls.company');
        return $org->calls;
    }
}
