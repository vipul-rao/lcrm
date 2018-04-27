<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface InviteUserRepository extends RepositoryInterface
{
    public function createInvite(array $data);
}
