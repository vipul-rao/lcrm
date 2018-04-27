<?php namespace App\Repositories;

use App\Models\Option;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
class OptionRepositoryEloquent extends BaseRepository implements OptionRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Option::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }


    public function getAll()
    {
        $options = $this->all();
        return $options;
    }
}
