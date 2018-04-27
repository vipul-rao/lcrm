<?php

namespace App\Repositories;

use App\Models\City;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class CityRepositoryEloquent extends BaseRepository implements CityRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return City::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
