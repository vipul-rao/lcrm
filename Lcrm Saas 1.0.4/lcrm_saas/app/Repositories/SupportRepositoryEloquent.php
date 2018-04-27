<?php

namespace App\Repositories;

use App\Models\Support;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class SupportRepositoryEloquent extends BaseRepository implements SupportRepository
{
    private $userRepository;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Support::class;
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

    public function getAll()
    {
        $this->generateParams();
        $emails = $this->userRepository->getOrganization()->supports()->get();

        return $emails;
    }

    public function markAsSolved(array $ids)
    {
        $this->generateParams();
        $tickets = $this->model->where('user_id', $this->userRepository->getUser()->id)
        ->whereIn('id', $ids)
        ->update(['status' => 'closed']);

        return $tickets;
    }

    public function markAsSolvedByAdmin(array $ids)
    {
        $this->generateParams();
        $tickets = $this->model->whereIn('id', $ids)
        ->update(['status' => 'closed']);

        return $tickets;
    }
}
