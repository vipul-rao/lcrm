<?php

namespace App\Http\Controllers\Users;

use App\Events\Subscription\CancelSubscription;
use App\Events\Subscription\ChangePlan;
use App\Events\Subscription\ResumeSubscription;
use App\Events\Subscription\SuspendSubscription;
use App\Events\Subscription\UpdateCard;
use App\Http\Controllers\Controller;
use App\Repositories\InviteUserRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
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
     * @var UserRepository
     */
    private $userRepository;

    private $payPlanRepository;

    private $inviteUserRepository;

    private $subscriptionRepository;

    private $settingsRepository;

    private $organizationSettingsRepository;
    /**
     * SubscriptionController constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository,
        PayPlanRepository $payPlanRepository,
        InviteUserRepository $inviteUserRepository,
        SubscriptionRepository $subscriptionRepository,
        SettingsRepository $settingsRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->inviteUserRepository = $inviteUserRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->settingsRepository = $settingsRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        view()->share('paypal_mode', $this->settingsRepository->getKey('paypal_mode'));
        view()->share('type', 'subscription');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization = $this->userRepository->getOrganization();
        $active_subscription = $organization->subscriptions->first();

        if (isset($active_subscription->subscription_type) && $active_subscription->subscription_type=='paypal'){
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $active_plan = $this->payPlanRepository->find($active_subscription->payplan_id);
            $recurring_payment_details = $provider->getRecurringPaymentsProfileDetails($active_subscription->profile_id);
            $subscription = $this->subscriptionRepository->find($active_subscription->id);
            $transactions = $subscription->paypalTransactions;
            $paypalTransactions = [];
            if (isset($transactions)){
                foreach ($transactions as $transaction){
                    $paypalTransactions[] = $provider->getTransactionDetails($transaction->txn_id);
                }
                view()->share('paypalTransactions',$paypalTransactions);
            }
            view()->share('recurring_payment_details',$recurring_payment_details);
            view()->share('subscription',$subscription);
        }
        else{
            if (isset($active_subscription)) {
                $active_plan = $this->payPlanRepository->all()->where('plan_id', $active_subscription->stripe_plan)->first();
            } else {
                $active_plan = $this->payPlanRepository->all()->where('id', $organization->generic_trial_plan)->first();
            }

            $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
            if (isset($stripe_secret) && $stripe_secret && isset($active_subscription->stripe_id)) {
                Stripe::setApiKey($stripe_secret);
                $subscriptions = Invoice::all([
                    'subscription' => $active_subscription->stripe_id,
                    'limit' => 100,
                ]);
                $subscription_customerid = Subscription::retrieve($active_subscription->stripe_id)->customer;
                $subscription_customer = Customer::retrieve($subscription_customerid);
                $events = Event::all([
                    'limit' => 100,
                ]);
                view()->share('events', $events);
                view()->share('subscription_customerid',$subscription_customerid);
                view()->share('subscriptions', $subscriptions);
                view()->share('subscription_customer', $subscription_customer);
            }
        }
//        return $recurring_payment_details;
        $this->generateParams();
        $title = trans('subscription.subscriptions');

        return view('user.subscription.index', compact('title', 'active_subscription', 'active_plan'));
    }

    public function changeSubscription()
    {
        $organization = $this->userRepository->getOrganization();

        $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $organization->id,
                'claimed_at' => null,
            ])->count();
        $active_subscription = $organization->subscriptions->first();
        if (isset($active_subscription->subscription_type) && $active_subscription->subscription_type=='paypal'){
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $active_plan = $this->payPlanRepository->find($active_subscription->payplan_id);
            $recurring_payment_details = $provider->getRecurringPaymentsProfileDetails($active_subscription->profile_id);
            view()->share('recurring_payment_details',$recurring_payment_details);
        }
        else{
            if (isset($active_subscription)) {
                $active_plan = $this->payPlanRepository->all()->where('plan_id', $active_subscription->stripe_plan)->first();
            } else {
                return redirect('subscription/change_generic_plan');
            }
        }
        $this->generateParams();
        $title = trans('subscription.change_subscriptions');

        return view('user.subscription.change', compact('title', 'active_subscription', 'organization', 'unanswered_invites', 'active_plan'));
    }

    public function changeGenericPlan()
    {
        if (count($this->userRepository->getOrganization()->subscriptions)) {
            return redirect('subscription/change');
        }
        view()->share('no_vue', true);
        $organization = $this->userRepository->getOrganization();
        $payplan = $this->payPlanRepository->find($organization->generic_trial_plan);
        $title = trans('subscription.change_plan');

        $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $organization->id,
                'claimed_at' => null,
            ])->count();
        $this->generateParams();

        return view('user.subscription.change_generic_plan', compact('title', 'payplan', 'unanswered_invites'));
    }

    public function changePlan($id)
    {
        $organization = $this->userRepository->getOrganization();
        $pay_plan = $this->payPlanRepository->find($id);

        $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $organization->id,
                'claimed_at' => null,
            ])->count();

        /**
         * redirect back if no subscriptions are present.
         */
        $active_subscription = $organization->subscriptions->first();
        if (!$active_subscription) {
            return redirect('subscription/change_generic_plan');
        }

        if (
            (($organization->staffWithUser->count() + $unanswered_invites) > $pay_plan->no_people)
        && $pay_plan->no_people
        ) {
            return redirect('subscription/change');
        }
        $subscription = $organization->subscriptions->first();

        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
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

        if ($subscription->subscription_type=='paypal'){
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $amount = ($pay_plan->amount/100)+(($pay_plan->amount/100)*$taxRate);
            $profileid = $subscription->profile_id;
            $startdate = now()->toAtomString();
            $subCount = $this->subscriptionRepository->all();
            $subCount = $subCount->count()+1;
            $now = now();
            $dataDo['subscription_desc'] = "Subscription #{$subCount}";
            $dataDo['invoice_id'] = $now;
            $dataDo['invoice_description'] = "Subscription #{$subCount}";
            $profile_desc = !empty($dataDo['subscription_desc']) ?
                $dataDo['subscription_desc'] : $dataDo['invoice_description'];
            $data = [
                'PROFILESTARTDATE' => $startdate,
                'DESC' => $profile_desc,
                'BILLINGPERIOD' => ucfirst($pay_plan->interval), // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
                'BILLINGFREQUENCY' => $pay_plan->interval_count, //
                'AMT' => $amount, // Billing amount for each billing cycle
                'CURRENCYCODE' => strtoupper($pay_plan->currency), // Currency code
            ];
            $response = $provider->updateRecurringPaymentsProfile($data, $profileid);
            if($response['ACK']=='Failure'){
                $profileid = $subscription->profile_id;
                $response = $provider->cancelRecurringPaymentsProfile($profileid);
                $subscription->status = 'Canceled';
                $subscription->ends_at = now();
                $subscription->save();
                return redirect('payment/'.$id.'/paypal');
            }else{
                $subscription->payplan_id = $pay_plan->id;
                $subscription->save();
                event(new ChangePlan($subscription->id));
                return redirect('subscription');
            }
        }else{
            $customer_id = $organization->stripe_id;
            $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
            try {
                if (isset($stripe_secret) && $stripe_secret) {
                    Stripe::setApiKey($stripe_secret);
                    if (isset($active_subscription->ends_at)) {
                        if (isset($active_subscription->ends_at) && now()->gt($active_subscription->ends_at)) {
                            $subscriptions = Subscription::create([
                                'customer' => $customer_id,
                                'items' => [
                                    [
                                        'plan' => $pay_plan->plan_id,
                                    ],
                                ],
                                'tax_percent' => $taxRate*100,
                            ]);
                            if ($europian_tax=='true' && $vat_number!=''){
                                $stripe_customer = Customer::retrieve($customer_id);
                                $stripe_customer->business_vat_id = $vat_number;
                                $stripe_customer->save();
                            }
                            $this->subscriptionRepository->create([
                                'organization_id' => $organization->id,
                                'name' => $pay_plan->name,
                                'stripe_id' => $subscriptions->id,
                                'stripe_plan' => $pay_plan->plan_id,
                                'subscription_type' => 'stripe',
                                'quantity' => 1,
                            ]);
                            flash(trans('subscription.created_successfully'), 'success');
                        } else {
                            $organization->subscription($subscription->name)
                                ->skipTrial()
                                ->swap($pay_plan->plan_id)
                                ->update([
                                    'name' => $pay_plan->name,
                                    ]);
                            if ($europian_tax=='true' && $vat_number!=''){
                                $stripe_customer = Customer::retrieve($customer_id);
                                $stripe_customer->business_vat_id = $vat_number;
                                $stripe_customer->save();
                            }
                            $stripe_subscription = Subscription::retrieve($active_subscription->stripe_id);
                            $stripe_subscription->tax_percent = $taxRate*100;
                            $stripe_subscription->save();
                            event(new ChangePlan($subscription->id));
                            flash(trans('subscription.updated_successfully'), 'success');
                        }

                        return redirect('subscription');
                    }
                    $organization->subscription($subscription->name)
                        ->skipTrial()
                        ->swap($pay_plan->plan_id)
                        ->update([
                            'name' => $pay_plan->name
                        ]);
                    if ($europian_tax=='true' && $vat_number!=''){
                        $stripe_customer = Customer::retrieve($customer_id);
                        $stripe_customer->business_vat_id = $vat_number;
                        $stripe_customer->save();
                    }
                    $stripe_subscription = Subscription::retrieve($active_subscription->stripe_id);
                    $stripe_subscription->tax_percent = $taxRate*100;
                    $stripe_subscription->save();

                    event(new ChangePlan($subscription->id));
                    flash(trans('subscription.updated_successfully'), 'success');
                }
            } catch (\Exception $e) {
                flash('Unknown error.', 'error');
            }
        }

        return redirect('subscription');
    }

    public function cancelSubscription()
    {
        $organization = $this->userRepository->getOrganization();
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

        return redirect('subscription');
    }

    public function suspendPaypalSubscription()
    {
        $organization = $this->userRepository->getOrganization();
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
        event(new SuspendSubscription($subscription->id));
        flash(trans('subscription.suspended_successfully'), 'success');
        return redirect('subscription');
    }

    public function resumeSubscription()
    {
        $organization = $this->userRepository->getOrganization();
        $subscription = $organization->subscriptions->first();
        $subscription = $organization->subscription($subscription->name)->resume();
        event(new ResumeSubscription($subscription->id));
        flash(trans('subscription.resumed_successfully'), 'success');

        return redirect('subscription');
    }

    public function updateCardIndex()
    {
        $organization = $this->userRepository->getOrganization();
        if (isset($organization->subscriptions) && $organization->subscriptions->count() == 0){
            return redirect('subscription');
        }
        $title = trans('subscription.update_card');
        view()->share('no_vue', true);

        return view('user.subscription.update_card', compact('title', $organization));
    }

    public function updateCard(Request $request)
    {
        $stripeToken = $request->stripeToken;
        $organization = $this->userRepository->getOrganization();
        if (isset($organization->subscriptions) && $organization->subscriptions->count() == 0){
            return redirect('subscription');
        }
        $organization->updateCard($stripeToken);

        event(new UpdateCard($organization->id));
        flash(trans('subscription.updated_card_successfully'))->success();

        return redirect('subscription');
    }

    private function generateParams()
    {
        $payplans = $this->payPlanRepository->all()->sortByDesc('organizations');

        $payment_plans_list = $payplans->values()->all();
        view()->share('stripe_secret', $this->settingsRepository->getKey('stripe_secret'));
        view()->share('stripe_publishable', $this->settingsRepository->getKey('stripe_publishable'));
        view()->share('payment_plans_list', $payment_plans_list);
    }
}
