<?php

namespace App\Repositories;

use App\Models\Opportunity;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

class OpportunityRepositoryEloquent extends BaseRepository implements OpportunityRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Opportunity::class;
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
        $org = $this->userRepository->getOrganization()->load('opportunities.salesTeam','opportunities.customer',
            'opportunities.calls','opportunities.meetings','opportunities.companies');
        $opportunities = $org->opportunities->where('is_archived',0)->where('is_delete_list',0)->where('is_converted_list',0);
        return $opportunities;
    }

    public function getArchived()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('opportunities.salesTeam','opportunities.customer',
            'opportunities.calls','opportunities.meetings','opportunities.companies');
        $opportunities = $org->opportunities->where('is_archived',1);
        return $opportunities;
    }

    public function getDeleted()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('opportunities.salesTeam','opportunities.customer',
            'opportunities.calls','opportunities.meetings','opportunities.companies');
        $opportunities = $org->opportunities->where('is_delete_list',1);
        return $opportunities;
    }

    public function getConverted()
    {
        $this->generateParams();
        $org = $this->userRepository->getOrganization()->load('opportunities.salesTeam','opportunities.customer',
            'opportunities.calls','opportunities.meetings','opportunities.companies');
        $opportunities = $org->opportunities->where('is_converted_list',1);
        return $opportunities;
    }

    public function getAllForCustomer($company_id)
    {
        $this->generateParams();
        $opportunities = $this->userRepository->getOrganization()->opportunities()->where([
            'is_archived'=>0,
            'is_delete_list'=>0,
            'is_converted_list'=>0,
            ['company_id','=', $company_id]
        ])->get();
        return $opportunities;
    }

    public function getMonthYear($created_at,$year)
    {
        $opportunities = $this->userRepository->getOrganization()->opportunities()->whereYear('created_at',$year)->whereMonth('created_at', $created_at)
            ->where([
                'is_archived'=>0,
                'is_delete_list'=>0,
                'is_converted_list'=>0
            ])->get();

        return $opportunities;
    }
}
