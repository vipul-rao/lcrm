<?php

namespace App\Repositories;

use App\Models\Meeting;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class MeetingRepositoryEloquent extends BaseRepository implements MeetingRepository
{
    private $userRepository;

    public function model()
    {
        return Meeting::class;
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
        $org = $this->userRepository->getOrganization()->load('meetings.responsible');
        $meetings = $org->meetings;
        return $meetings;
    }
}
