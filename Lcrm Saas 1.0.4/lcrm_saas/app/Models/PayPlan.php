<?php

namespace App\Models;

use App\Repositories\OrganizationRepositoryEloquent;
use App\Repositories\SubscriptionRepositoryEloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class PayPlan extends Model implements Transformable
{
    use SoftDeletes,RevisionableTrait,TransformableTrait;

    private $organizationRepository;
    private $subscriptionRepository;


    protected $dates = ['deleted_at'];
    protected $guarded = ['id'];
    protected $table = 'pay_plans';
    protected $appends = ['organizations'];


    public function currency()
    {
        return $this->hasOne(Currency::class, 'currency_id');
    }

    public function getOrganizationsAttribute()
    {
        $this->organizationRepository = new OrganizationRepositoryEloquent(app());
        $this->subscriptionRepository = new SubscriptionRepositoryEloquent(app());

        $genericplanOrganizationsTotal = $this->organizationRepository->findByField('generic_trial_plan', $this->id)->count();
        $genericplanOrganizationsExpired = $this->organizationRepository->ExpiredGenericTrial()->where('generic_trial_plan', $this->id)->count();
        $subscribedPlansTotal = $this->subscriptionRepository->findByField('stripe_plan', $this->plan_id)->count();
        $subscribedPlansExpired = $this->subscriptionRepository->expiredSubscriptions()->where('stripe_plan', $this->plan_id)->count();
        $subscribedPlansByPaypalTotal = $this->subscriptionRepository->findByField('payplan_id', $this->id)->count();
        $subscribedPlansByPaypalExpired = $this->subscriptionRepository->expiredSubscriptions()->where('payplan_id', $this->id)->count();
        $genericplanOrganizations = $genericplanOrganizationsTotal - $genericplanOrganizationsExpired;
        $subscribedPlans = $subscribedPlansTotal - $subscribedPlansExpired;
        $subscribedByPaypalPlans = $subscribedPlansByPaypalTotal - $subscribedPlansByPaypalExpired;
        return $genericplanOrganizations + $subscribedPlans + $subscribedByPaypalPlans;
    }
}
