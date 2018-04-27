<?php

namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface LeadRepository extends RepositoryInterface
{
    public function getAll();

    public function getMonthYearWithUser($created_at,$year,$organization_id);

    public function getMonthYear($monthno, $year);
}