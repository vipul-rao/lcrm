<?php

namespace App\Repositories;

use App\Models\ContactUs;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class ContactUsRepositoryEloquent extends BaseRepository implements ContactUsRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return ContactUs::class;
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
}
