<?php

namespace App\Repositories;

use App\Models\EmailTemplate;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class EmailTemplateRepositoryEloquent extends BaseRepository implements EmailTemplateRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     */
    public function model()
    {
        return EmailTemplate::class;
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
        $emailTemplates = $this->userRepository->getOrganization()->emailTemplates()->get();
        return $emailTemplates;
    }

    public function getAllForUser()
    {
        $this->generateParams();
        $emailTemplates = $this->userRepository->getUser()->emailTemplates;
        return $emailTemplates;
    }
}
