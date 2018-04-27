<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface SubscriptionRepository extends RepositoryInterface
{
    public function getSubscriptionsByMonthYear($monthno,$year);

    public function activeSubscriptions();

    public function trialingSubscriptions();

    public function expiredSubscriptions();

    public function expiredTrials();
}
