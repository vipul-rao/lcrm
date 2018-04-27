<?php
namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface OptionRepository extends RepositoryInterface
{
    public function getAll();

}