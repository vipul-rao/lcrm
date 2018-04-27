<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface SupportRepository extends RepositoryInterface
{
    public function getAll();

    public function markAsSolved(array $ids);

    public function markAsSolvedByAdmin(array $ids);
}
