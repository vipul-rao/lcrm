<?php

namespace App\Repositories;

use App\Models\Subscription;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class SubscriptionRepositoryEloquent extends BaseRepository implements SubscriptionRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Subscription::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getSubscriptionsByMonthYear($monthno,$year)
    {
        $subscriptions = $this->model
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthno)
            ->whereYear('created_at', now())
            ->get();

        return $subscriptions;
    }

    public function activeSubscriptions()
    {
        $subscriptions = $this->model->load('organization')
            ->whereDate('ends_at', '>', now())
            ->orWhereNull('ends_at')
            ->whereNull('trial_ends_at')
            ->get();

        return $subscriptions;
    }

    public function trialingSubscriptions()
    {
        $subscriptions = $this->model->load('organization')
            ->whereNotNull('trial_ends_at')
            ->whereDate('trial_ends_at', '>', now())
            ->get();
        return $subscriptions;
    }

    public function expiredSubscriptions()
    {
        $subscriptions = $this->model->load('organization')
            ->whereDate('ends_at', '<', now())
            ->orWhere('status','=','Canceled')
            ->get();

        return $subscriptions;
    }

    public function expiredTrials()
    {
        $subscriptions = $this->model->load('organization')
            ->whereDate('trial_ends_at', '<', now())
            ->get();

        return $subscriptions;
    }
}
