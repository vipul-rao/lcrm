<?php
namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface CallRepository extends RepositoryInterface
{
    public function getAll();

}