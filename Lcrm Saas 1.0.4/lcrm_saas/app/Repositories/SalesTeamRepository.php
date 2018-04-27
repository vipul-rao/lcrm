<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface SalesTeamRepository extends RepositoryInterface
{
    public function getAll();

    public function teamLeader();

    public function createTeam(array $data);

    public function updateTeam(array $data,$salesteam_id);

    public function deleteTeam($deleteteam);

    public function findTeam($team_id);

    public function getMonthYear($monthno, $year);
}
