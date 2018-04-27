<?php

namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;


interface EmailRepository extends RepositoryInterface
{
    public function getAll();

}