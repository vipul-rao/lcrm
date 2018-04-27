<?php

namespace App\Repositories;

use App\Models\Salesteam;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class SalesTeamRepositoryEloquent extends BaseRepository implements SalesTeamRepository
{
    private $userRepository;
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Salesteam::class;
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
        $org = $this->userRepository->getOrganization()->load('salesteams.actualInvoice');
        return $org->salesteams;
    }

    public function teamLeader()
    {
        return $this->model->teamLeader();
    }

    public function createTeam(array $data)
    {
        $this->generateParams();
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $data['user_id']= $user->id;
        $data['organization_id']= $organization->id;

        $team = collect($data)->except('team_members')->toArray();
        $salesTeam = $this->create($team);

        $salesTeam->members()->attach($data['team_members']);
    }

    public function updateTeam(array $data,$salesteam_id)
    {
        $this->generateParams();
        $team = collect($data)->except('team_members')->toArray();
        $salesTeam = $this->update($team,$salesteam_id);
        $salesTeam->members()->sync($data['team_members']);
    }

    public function deleteTeam($deleteteam)
    {
        $this->generateParams();
        $this->delete($deleteteam);
    }

    public function findTeam($team_id){
        $team=$this->with('members')->find($team_id);
        return $team;
    }

    public function getMonthYear($monthno, $year)
    {
        $this->generateParams();
        $salesTeam = $this->userRepository->getOrganization()->salesteams()->whereYear('created_at', $year)->whereMonth('created_at', $monthno)->get();
        return $salesTeam;
    }
}
