<?php

namespace App\Repositories;

use App\Models\PayPlan;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Stripe\Stripe;
use Stripe\Plan;
use Stripe\Product;

class PayPlanRepositoryEloquent extends BaseRepository implements PayPlanRepository
{
    private $settingsRepository;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return PayPlan::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function generateParams()
    {
        $this->settingsRepository = new SettingsRepositoryEloquent(app());

        $stripe = new Stripe();

        $stripe->setApiKey($this->settingsRepository->getKey('stripe_secret'));
    }

    public function createPlan($request)
    {
        $this->generateParams();
        try {
            $product = Product::all([
                'limit' => 3,
            ]);

            if (!count($product->data)) {
                $product = Product::create([
                    'name' => $this->settingsRepository->getKey('site_name'),
                    'type' => 'service',
                ]);
                $product = Product::all([
                    'limit' => 3,
                ]);
            }
        } catch (\Exception $e) {
            logger($e);
        }

        if (!isset($product->data)) {
            flash('No Product')->error();

            return null;
        }

        $productId = $product->data[0]->id;

        $stripePlan = Plan::create($request->except('_token', 'name', 'statement_descriptor', 'no_people', 'is_credit_card_required', 'is_visible') + [
            'product' => $productId,
            'metadata' => [
                'name' => $request->name,
                'statement_descriptor' => $request->statement_descriptor,
                'no_people' => $request->no_people,
                'is_credit_card_required' => $request->is_credit_card_required,
                'is_visible' => $request->is_visible,
            ],
        ]);

        return $this->savePlan($stripePlan->id);
    }

    public function updatePlan($request, $plan)
    {
        $this->generateParams();

        $stripePlan = Plan::retrieve($plan->plan_id);

        $stripePlan->metadata->name = $request->name;
        $stripePlan->metadata->no_people = $request->no_people;
        $stripePlan->metadata->statement_descriptor = $request->statement_descriptor ?? null;
        $stripePlan->trial_period_days = $request->trial_period_days;
        $stripePlan->metadata->is_credit_card_required = $request->is_credit_card_required;
        $stripePlan->metadata->is_visible = $request->is_visible;
        $stripePlan->save();

        return $this->savePlan($stripePlan->id);
    }

    public function deletePlan($plan)
    {
        $this->generateParams();

        $stripePlan = Plan::retrieve($plan->plan_id);

        $deleted = $stripePlan->delete();

        if ($deleted->deleted) {
            $plan->delete();
        }

        return;
    }

    public function savePlan($plan_id)
    {
        $plan = Plan::retrieve($plan_id);
        $payPlan = PayPlan::updateOrCreate(
            ['plan_id' => $plan->id],
            [
                'name' => $plan->metadata->name,
                'amount' => $plan->amount,
                'currency' => $plan->currency,
                'interval' => $plan->interval,
                'interval_count' => $plan->interval_count,
                'no_people' => $plan->metadata->no_people,
                'statement_descriptor' => $plan->metadata->statement_descriptor,
                'trial_period_days' => $plan->trial_period_days,
                'is_credit_card_required' => $plan->metadata->is_credit_card_required,
                'is_visible' => $plan->metadata->is_visible,
            ]
         );

        return $payPlan;
    }
}
