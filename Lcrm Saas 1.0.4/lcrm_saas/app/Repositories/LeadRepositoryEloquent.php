<?php

namespace App\Repositories;

use App\Models\Lead;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class LeadRepositoryEloquent extends BaseRepository implements LeadRepository
{

    private $userRepository;

    public function model()
    {
        return Lead::class;
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
        $org = $this->userRepository->getOrganization()->load('leads.calls');
        return $org->leads;
    }

    public function getMonthYearWithUser($created_at,$year, $organization_id)
    {
        $leads = $this->model->whereYear('created_at', $year)->whereMonth('created_at', $created_at)->where('organization_id',$organization_id)->get();

        return $leads;
    }

    public function getMonthYear($monthno, $year)
    {
        $leads = $this->userRepository->getOrganization()->leads()->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->get();

        return $leads;
    }
}
