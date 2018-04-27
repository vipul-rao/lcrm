<?php

namespace App\Http\Controllers\Admin;

use App\Events\Subscription\CancelSubscription;
use App\Events\Subscription\ChangePlan;
use App\Events\Subscription\ResumeSubscription;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSubscriptionRequest;
use App\Http\Requests\StaffRequest;
use App\Repositories\InviteUserRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;
use App\Events\Subscription\Extend;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Srmklive\PayPal\Facades\PayPal;
use Stripe\Customer;
use Stripe\Event;
use Stripe\Invoice;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionController extends Controller
{
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;
    private $organizationRepository;
    private $payPlanRepository;
    private $inviteUserRepository;
    private $settingsRepository;
    private $organizationSettingsRepository;

    /**
     * SubscriptionController constructor.
     *
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserRepository         $userRepository
     * @param OptionRepository       $optionRepository
     */
    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository,
        OptionRepository $optionRepository,
        OrganizationRepository $organizationRepository,
        PayPlanRepository $payPlanRepository,
        SettingsRepository $settingsRepository,
        InviteUserRepository $inviteUserRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository = $userRepository;
        $this->optionRepository = $optionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->inviteUserRepository = $inviteUserRepository;
        $this->settingsRepository = $settingsRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;

        view()->share('type', 'admin/subscription');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('subscription.active_subscriptions');

        return view('admin.subscription.index', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StaffRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AdminSubscriptionRequest $request)
    {
        $user = $this->userRepository->find($request->user_id);

        $organization = $user->organizations()->first();
        $this->subscriptionRepository->create([
            'organization_id' => $organization->id,
            'name' => $organization->name,
            'stripe_id' => $organization->stripe_id,
            'stripe_plan' => 'basic',
            'quantity' => 1,
            'ends_at' => $request->ends_at,
        ]);

        return redirect('admin/subscription');
    }

    public function update(AdminSubscriptionRequest $request, $subscription)
    {
        $subscription = $this->subscriptionRepository->find($subscription);
        $subscription->update($request->all());

        return redirect('admin/subscription');
    }

    public function show($subscription)
    {
        $subscription = $this->subscriptionRepository->find($subscription);
        $title = trans('subscription.show_subscription');
        $action = 'show';
        $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
        if (isset($stripe_secret) && $stripe_secret && isset($subscription->stripe_id)) {
            Stripe::setApiKey($stripe_secret);
            $subscriptions = Invoice::all([
                'subscription' => $subscription->stripe_id,
                'limit' => 100,
            ]);
            $subscription_customerid = Subscription::retrieve($subscription->stripe_id)->customer;
            $subscription_customer = Customer::retrieve($subscription_customerid);
            $events = Event::all([
                'limit' => 100,
            ]);
            view()->share('events', $events);
            view()->share('subscription_customerid',$subscription_customerid);
            view()->share('subscriptions', $subscriptions);
            view()->share('subscription_customer', $subscription_customer);
        }
        if ($subscription->subscription_type=='paypal'){
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $recurring_payment_details = $provider->getRecurringPaymentsProfileDetails($subscription->profile_id);
            view()->share('recurring_payment_details',$recurring_payment_details);
        }

        return view('admin.subscription.show', compact('title', 'subscription', 'action'));
    }

    public function data()
    {
        $subscriptions = $this->subscriptionRepository->activeSubscriptions()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'org_name' => $subscription->organization->name,
                    'org_email' => $subscription->organization->email,
                    'plan' => $subscription->name,
                    'subscription_type' => $subscription->subscription_type,
                    'ends_at' => $subscription->ended_at ? $subscription->ended_at : trans('subscription.subscription_active'),
                ];
            });

        return DataTables::of($subscriptions)
            ->addColumn('actions', '<a href="{{ url(\'admin/subscription/\' . $id . \'/active\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     <a href="{{ url(\'admin/subscription/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    public function trialing()
    {
        $title = trans('subscription.trialing_subscriptions');

        return view('admin.subscription.trialing', compact('title'));
    }

    public function trialingData()
    {
        $subscriptions = $this->subscriptionRepository->trialingSubscriptions()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'org_name' => $subscription->organization->name,
                    'org_email' => $subscription->organization->email,
                    'plan' => $subscription->name,
                    'trial_ends_at' => $subscription->trial_ends_at,
                ];
            });
        $organizations = $this->organizationRepository->onGenericTrial()
            ->map(function ($organization) {
                return [
                    'id' => $organization->id,
                    'org_name' => $organization->name,
                    'org_email' => $organization->email,
                    'plan' => isset($organization->genericPlan) ? $organization->genericPlan->name : null,
                    'trial_ends_at' => $organization->trial_ends,
                ];
            });

        $subscriptionData = [];
        foreach ($subscriptions as $subscription) {
            $subscriptionData[] = $subscription;
        }
        $organizationData = [];
        foreach ($organizations as $key => $organization) {
            $organizationData[] = $organization;
        }
        $data = array_merge($subscriptionData, $organizationData);

        return DataTables::of($data)
            ->addColumn('actions', '<a href="{{ url(\'admin/subscription/\' . $id . \'/active\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     <a href="{{ url(\'admin/subscription/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    public function expiredSubscription()
    {
        $title = trans('subscription.expired_subscriptions');

        return view('admin.subscription.expired_subscription', compact('title'));
    }

    public function expiredSubscriptionData()
    {
        $subscriptions = $this->subscriptionRepository->expiredSubscriptions()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'org_name' => $subscription->organization->name,
                    'org_email' => $subscription->organization->email,
                    'plan' => $subscription->name,
                    'subscription_type' => $subscription->subscription_type,
                    'ends_at' => $subscription->ended_at ? $subscription->ended_at : trans('subscription.subscription_active'),
                ];
            });

        return DataTables::of($subscriptions)
            ->addColumn('actions', '<a href="{{ url(\'admin/subscription/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    public function expiredTrial()
    {
        $title = trans('subscription.expired_trials');

        return view('admin.subscription.expired_trial', compact('title'));
    }

    public function expiredTrialData()
    {
        $subscriptions = $this->subscriptionRepository->expiredTrials()
            ->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'org_name' => $subscription->organization->name,
                    'org_email' => $subscription->organization->email,
                    'plan' => $subscription->name,
                    'subscription_type' => $subscription->subscription_type,
                    'trial_ends_at' => $subscription->trial_ends_at,
                ];
            });

        $organizations = $this->organizationRepository->ExpiredGenericTrial()
            ->map(function ($organization) {
                $pay_plan = $this->payPlanRepository->find($organization->generic_trial_plan);
                if (!$pay_plan->trial_period_days && !$pay_plan->is_credit_card_required) {
                    return null;
                }

                return [
                    'id' => $organization->id,
                    'org_name' => $organization->name,
                    'org_email' => $organization->email,
                    'subscription_type' => $organization->subscription_type,
                    'plan' => isset($organization->genericPlan) ? $organization->genericPlan->name : null,
                    'trial_ends_at' => $organization->trial_ends,
                ];
            });

        $subscriptionData = [];
        foreach ($subscriptions as $subscription) {
            $subscriptionData[] = $subscription;
        }
        $organizationData = [];
        foreach ($organizations as $key => $organization) {
            if (isset($organization)) {
                $organizationData[] = $organization;
            }
        }
        $data = array_merge($subscriptionData, $organizationData);

        return DataTables::of($data)
            ->addColumn('actions', '<a href="{{ url(\'admin/subscription/\' . $id . \'/active\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     <a href="{{ url(\'admin/subscription/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    public function activeSubscription($subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $organization = $this->organizationRepository->find($subscription->organization_id);
        $active_subscription = $organization->subscriptions->first();

        $this->generateParams();
        $title = trans('subscription.subscription_active');

        return view('admin.subscription.active', compact('title', 'subscription', 'users', 'active_subscription'));
    }

    public function changeSubscription($subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $organization = $this->organizationRepository->find($subscription->organization_id);
        $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $organization->id,
                'claimed_at' => null,
            ])->count();
        $active_subscription = $organization->subscriptions->first();
        if (isset($active_subscription)) {
            $active_plan = $this->payPlanRepository->all()->where('plan_id', $active_subscription->stripe_plan)->first();
        } else {
            $active_plan = $this->payPlanRepository->all()->where('id', $organization->generic_trial_plan)->first();
        }
        $this->generateParams();
        $title = trans('subscription.change_subscriptions');

        return view('admin.subscription.change', compact('title', 'subscription', 'active_subscription', 'organization', 'unanswered_invites', 'active_plan'));
    }

    public function changePlan($subscription, $id)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $organization = $this->organizationRepository->find($subscription->organization_id);

        $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $organization->id,
                'claimed_at' => null,
            ])->count();

        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        $orgSettings = $this->organizationSettingsRepository->findByField('organization_id',$organization->id)->pluck('value','key');
        if (isset($orgSettings['vat_number'])){
            $vat_number = $orgSettings['vat_number'];
        }else{
            $vat_number = '';
        }
        if ($europian_tax=='true'){
            $countryCode = config('settings.country_code');
            if ($vat_number!=''){
                $taxRate = 0;
            }else{
                $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
            }
        }else{
            $taxRate = 0;
        }

        $pay_plan = $this->payPlanRepository->find($id);
        if (
            (($organization->staffWithUser->count() + $unanswered_invites) > $pay_plan->no_people)
            && $pay_plan->no_people
        ) {
            return redirect('admin/subscription/'.$organization->id.'/change');
        }
        $subscription = $organization->subscriptions->first();
        $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
        try {
            if (isset($stripe_secret) && $stripe_secret) {
                Stripe::setApiKey($stripe_secret);
                $organization->subscription($subscription->name)
                    ->skipTrial()
                    ->swap($pay_plan->plan_id)
                    ->update(['name' => $pay_plan->name]);
                if ($europian_tax=='true' && $vat_number!=''){
                    $stripe_customer = Customer::retrieve($organization->stripe_id);
                    $stripe_customer->business_vat_id = $vat_number;
                    $stripe_customer->save();
                }
                $stripe_subscription = Subscription::retrieve($subscription->stripe_id);
                $stripe_subscription->tax_percent = $taxRate*100;
                $stripe_subscription->save();
                event(new ChangePlan($subscription->id));
                flash(trans('subscription.updated_successfully'), 'success');
            }
        } catch (\Exception $e) {
            flash(trans('subscription.change_error'), 'error');
        }

        return redirect('admin/subscription');
    }

    public function cancelSubscription($subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $organization = $this->organizationRepository->find($subscription->organization_id);
        $subscription = $organization->subscriptions->first();
        if ($subscription->subscription_type=='paypal'){
//            Cancel recurring payment profile
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $profileid = $subscription->profile_id;
            $response = $provider->cancelRecurringPaymentsProfile($profileid);
            $subscription->status = 'Canceled';
            $subscription->ends_at = now();
            $subscription->save();
        }else{
            $subscription = $organization->subscription($subscription->name)->cancel();
        }
        event(new CancelSubscription($subscription->id));
        flash(trans('subscription.canceled_successfully'), 'success');

        return redirect('admin/subscription');
    }

    public function suspendPaypalSubscription($subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $organization = $this->organizationRepository->find($subscription->organization_id);
        $subscription = $organization->subscriptions->first();
//            Suspend recurring payment profile
        $provider = PayPal::setProvider('express_checkout');
        $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
        if (!isset($paypal_mode)){
            flash(trans('subscription.paypal_keys_are_required'))->error();
            return redirect()->back();
        }
        $profileid = $subscription->profile_id;
        $response = $provider->suspendRecurringPaymentsProfile($profileid);
        $subscription->status = 'Suspended';
        $subscription->save();
        event(new CancelSubscription($subscription->id));
        flash(trans('subscription.canceled_successfully'), 'success');
        return redirect('subscription');
    }

    public function resumeSubscription($subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $organization = $this->organizationRepository->find($subscription->organization_id);
        $subscription = $organization->subscriptions->first();
        if ($subscription->subscription_type=='paypal') {
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $profileid = $subscription->profile_id;
            $response = $provider->reactivateRecurringPaymentsProfile($profileid);
            $subscription->status = 'Active';
            $subscription->save();
        }else{
            $subscription = $organization->subscription($subscription->name)->resume();
        }

        event(new ResumeSubscription($subscription->id));
        flash(trans('subscription.resumed_successfully'), 'success');

        return redirect('admin/subscription');
    }

    public function extendSubscription($subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $title = 'Extend Subscription';
        $organization = $this->organizationRepository->find($subscription->organization_id);
        $subscription = $organization->subscriptions->first();

        return view('admin.subscription.extend', compact('title', 'subscription', 'organization'));
    }

    public function postExtendSubscription(Request $request, $subscription)
    {
        $subscription = $this->subscriptionRepository->activeSubscriptions()->find($subscription);
        if (!isset($subscription)) {
            return redirect('admin/subscription');
        }
        $validatedData = $request->validate([
            'duration' => 'required|numeric|min:1',
            'reason' => 'required',
        ]);

        Stripe::setApiKey($this->settingsRepository->getKey('stripe_secret'));

        $organization = $this->organizationRepository->find($subscription->organization_id);
        $subscription = $organization->subscriptions->first();

        $subscription = \Stripe\Subscription::retrieve($subscription->stripe_id);

        $newSubscription = \Stripe\Subscription::update($subscription->id, [
            'trial_end' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->addDays($request->duration)->timestamp,
            'prorate' => false,
        ]);

        event(new Extend([
            'subscription' => $subscription,
            'organization' => $organization,
            'duration' => $request->duration,
            'reason' => $request->reason,
        ]));
        flash(trans('subscription.extended_successfully'), 'success');

        return redirect('admin/subscription');
    }

    private function generateParams()
    {
        $payplans = $this->payPlanRepository->all()->sortByDesc('organizations');

        $payment_plans_list = $payplans->values()->all();
        view()->share('payment_plans_list', $payment_plans_list);
    }
}
