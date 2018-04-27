<?php
namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface OpportunityRepository extends RepositoryInterface
{
    public function getAll();

    public function getArchived();

    public function getDeleted();

    public function getConverted();

    public function getAllForCustomer($company_id);

    public function getMonthYear($created_at,$year);
}