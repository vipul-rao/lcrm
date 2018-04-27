<?php

namespace App\Repositories;

use App\Models\Email;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class EmailRepositoryEloquent extends BaseRepository implements EmailRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Email::class;
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
        $emails = $this->userRepository->getOrganization()->emails()->get();
        return $emails;
    }

}
