<?php

namespace App\Repositories;

use App\Models\State;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class StateRepositoryEloquent extends BaseRepository implements StateRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return State::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
