<?php

namespace App\Repositories;

use App\Models\Reply;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class ReplyRepositoryEloquent extends BaseRepository implements ReplyRepository
{
    private $userRepository;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Reply::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams()
    {
        $this->userRepository = new UserRepositoryEloquent(app());
    }
}
