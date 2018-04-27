<?php

namespace App\Repositories;
use Prettus\Repository\Contracts\RepositoryInterface;

interface EmailTemplateRepository extends RepositoryInterface
{
    public function getAll();

    public function getAllForUser();

}