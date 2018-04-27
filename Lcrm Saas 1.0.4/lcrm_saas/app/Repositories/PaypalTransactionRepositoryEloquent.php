<?php

namespace App\Repositories;

use App\Models\PaypalTransaction;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class PaypalTransactionRepositoryEloquent extends BaseRepository implements PaypalTransactionRepository
{
    private $userRepository;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return PaypalTransaction::class;
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
