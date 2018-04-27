<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\OrganizationRepositoryEloquent;
use App\Repositories\OrganizationRolesRepositoryEloquent;
use App\Repositories\PayPlanRepositoryEloquent;

class VerifySubscription
{
    private $userRepository;
    private $organizationRepository;
    private $organizationRolesRepository;

    public $user;
    public $organization;
    /**
     * Enter the urls here that don't need a subscription for the user to access.
     * Eg: user don't need a subscription to renew a plan.
     */
    public $allowedUrls = [
        '^payment',
        '^subscription/change',
        'change_generic_plan',
        'update_card',
        'change_subscription',
        'resume_paypal_subscription',
        'setting'
    ];

    public function __construct()
    {
        $this->userRepository = new UserRepositoryEloquent(app());

        $this->organizationRepository = new OrganizationRepositoryEloquent(app());

        $this->organizationRolesRepository = new OrganizationRolesRepositoryEloquent(app());
    }

    public function handle($request, Closure $next)
    {
        $this->user = $this->userRepository->getUser();
        $this->organization = $this->userRepository->getOrganization();
        if ($this->organization && 1 === $this->organization->is_deleted) {
            flash('Something wrong, Please contact the admin')->error();

            return redirect('/');
        }
        if ($this->organization && $this->organization->generic_trial_plan) {
            $plan = (new PayPlanRepositoryEloquent(app()))->find($this->organization->generic_trial_plan);
            if (!$plan->trial_period_days && !$plan->is_credit_card_required) {
                return $next($request);
            }
        }
        if ($this->organization &&
            (count($this->organization->subscriptions) || $this->organization->onGenericTrial())
        ) {
            $name = $this->organization->subscriptions->first()->name ?? (new PayPlanRepositoryEloquent(app()))->find($this->organization->generic_trial_plan);

            if (
            (!$this->organization->subscribed($name)
                &&
                !$this->organization->onGenericTrial()) || ( isset($this->organization->subscriptions->first()->status) && $this->organization->subscriptions->first()->status=='Canceled' )
            || ( isset($this->organization->subscriptions->first()->status) && $this->organization->subscriptions->first()->status=='Suspended' )
            ) {
                return $this->noSubscription($request, $next);
            }

            // https://github.com/laravel/cashier/issues/486
            if (
                $this->organization->subscribed($name)
                &&
                (
                    null !== $this->organization->subscriptions->first()->trial_ends_at
                    &&
                    !$this->organization->subscriptions->first()->onTrial()
                )
            ) {
                return $this->noSubscription($request, $next);
            }
            // return to home if already subscribed
            if (
                1 === preg_match('#^payment#', $request->path())
                ||
                (1 === preg_match('#change_generic_plan#', $request->path()) && !$this->organization->onGenericTrial())
            ) {
                return redirect('dashboard');
            }

            return $next($request);
        } elseif ($this->organization) {
            return $this->noSubscription($request, $next);
        } else {
            return back()->withErrors(['message' => 'Permission denied']);
        }

        // ==============
        return $next($request);
    }

    public function noSubscription($request, $next)
    {
        /*
         * If the user is in an organization and not subscribed to any plan.
         */
        $role = $this->organizationRolesRepository->getRole($this->organization, $this->user);
        if ('admin' === $role) {
            /*
             * If the user is admin of organization and not subscribed to any plan.
             */
            if ($this->unSubscribedAccess($request)) {
                return $next($request);
            }
            if (count($this->organization->subscriptions)) {
                return redirect('subscription/change')->withErrors(['message' => 'You need a subscription.']);
            } elseif (isset($this->organization->trial_ends_at)) {
                return redirect('subscription/change_generic_plan')->withErrors(['message' => 'You need a subscription.']);
            }

            return redirect('payment')->withErrors(['message' => 'You need a subscription.']);
        } else {
            /*
             * If the user is not admin of organization and not subscribed to any plan.
             */
            return redirect('subscription-expired');
        }
    }

    public function unSubscribedAccess($request)
    {
        foreach ($this->allowedUrls as $url) {
            if (1 === preg_match('#'.$url.'#', $request->path())) {
                return true;
            }
        }

        return false;
    }
}
