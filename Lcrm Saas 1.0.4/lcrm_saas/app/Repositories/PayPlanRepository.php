<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface PayPlanRepository extends RepositoryInterface
{
    public function createPlan($request);

    public function updatePlan($request, $plan);
}
