<?php

namespace App\Http\Controllers\Users;

use App\Events\Subscription\SubscriptionCreated;
use App\Events\Subscription\TrialWithoutCard;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Repositories\SettingsRepository;
use Stripe\Customer;
use Stripe\Stripe;

class PaymentController extends Controller
{
    /**
     * @var PayPlanRepository
     */
    private $payPlanRepository;

    private $userRepository;

    private $settingsRepository;

    private $subscriptionRepository;

    private $organizationSettingsRepository;
    /*
     * PaymentController constructor.
     *
     * @param PayPlanRepository     $payPlanRepository
     * @param OrganizationSettingRepository $OrganizationSettingRepository
     */
    public function __construct(
        PayPlanRepository $payPlanRepository,
        UserRepository $userRepository,
        SettingsRepository $settingsRepository,
        SubscriptionRepository $subscriptionRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();

        $this->payPlanRepository = $payPlanRepository;

        $this->userRepository = $userRepository;

        $this->settingsRepository = $settingsRepository;

        $this->subscriptionRepository = $subscriptionRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;

        view()->share('stripe_secret', $this->settingsRepository->getKey('stripe_secret'));
        view()->share('stripe_publishable', $this->settingsRepository->getKey('stripe_publishable'));
        view()->share('paypal_mode', $this->settingsRepository->getKey('paypal_mode'));
        view()->share('type', 'payment');
    }

    public function pay()
    {
        if (count($this->userRepository->getOrganization()->subscriptions)) {
            return redirect('subscription/change');
        }
        view()->share('no_vue', true);

        $title = trans('userpayment.payment_subscription');
        $this->generateParams();
        return view('user.payment.pay', compact('title'));
    }

    public function stripe(SubscriptionRequest $request)
    {
        $organization = $this->userRepository->getOrganization();
        $payment = $this->payPlanRepository->find($request->pay_plan);
        $creditCardToken = $request->stripeToken;
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
        $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
        Stripe::setApiKey($stripe_secret);
        if (!$payment->trial_period_days) {
            $subscription = $organization->newSubscription($payment->name, $payment->plan_id)
            ->create($creditCardToken);
            $organization->subscription_type = 'stripe';
            $organization->save();
            $subscription->subscription_type = 'stripe';
            $subscription->save();
            if ($europian_tax=='true' && $vat_number!=''){
                $stripe_customer = Customer::retrieve($organization->stripe_id);
                $stripe_customer->business_vat_id = $vat_number;
                $stripe_customer->save();
            }
            event(new SubscriptionCreated($subscription->id));
        } else {
            $subscription = $organization->newSubscription($payment->name, $payment->plan_id)
                        ->trialDays($payment->trial_period_days)
                        ->create($creditCardToken);
            $organization->subscription_type = 'stripe';
            $organization->save();
            $subscription->subscription_type = 'stripe';
            $subscription->save();
            if ($europian_tax=='true' && $vat_number!=''){
                $stripe_customer = Customer::retrieve($organization->stripe_id);
                $stripe_customer->business_vat_id = $vat_number;
                $stripe_customer->save();
            }
            event(new SubscriptionCreated($subscription->id));
        }
        // @QUESTION Do we need to send a mail here
        return redirect('payment/success');
    }

    public function stripeWithoutTrial(SubscriptionRequest $request)
    {
        $organization = $this->userRepository->getOrganization();
        $payment = $this->payPlanRepository->find($request->pay_plan);
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        $creditCardToken = $request->stripeToken;
        $subscription = $organization->newSubscription($payment->name, $payment->plan_id)
        ->skipTrial()
        ->create($creditCardToken);
        event(new SubscriptionCreated($subscription->id));

        $organization->trial_ends_at = null;
        $organization->generic_trial_plan = null;
        $organization->created_by_admin = 0;
        $organization->subscription_type = 'stripe';
        $organization->save();
        $subscription->subscription_type = 'stripe';
        $subscription->save();
        $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
        $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
        Stripe::setApiKey($stripe_secret);
        if ($europian_tax=='true' && $vat_number!=''){
            $stripe_customer = Customer::retrieve($organization->stripe_id);
            $stripe_customer->business_vat_id = $vat_number;
            $stripe_customer->save();
        }

        return redirect('payment/success');
    }

    public function stripeWithoutCard($id)
    {
        $organization = $this->userRepository->getOrganization();
        $payment = $this->payPlanRepository->find($id);
        $organization->trial_ends_at = now()->addDays($payment->trial_period_days);
        $organization->generic_trial_plan = $payment->id;
        $organization->subscription_type = 'stripe';
        $organization->save();
        event(new TrialWithoutCard($organization->id));

        return redirect('payment/success');
    }

    public function success()
    {
        $title = trans('userpayment.subscription_finish');

        return view('user.payment.success', compact('title'));
    }

    public function cancel($subscription_id)
    {
        $subscription = $this->subscriptionRepository->find($subscription_id);
        if ('stripe' == $subscription->payment_method) {
            $subscription->subscription()->cancel();
        }
        $subscription->ends_at = now();
        $subscription->status = trans('userpayment.cancel');
        $subscription->save();

        return redirect('payment/status');
    }

    private function generateParams()
    {
        $payment_plans = $this->payPlanRepository->all()->pluck('name', 'id');
        view()->share('payment_plans', $payment_plans);

        $payplans = $this->payPlanRepository->all()->sortByDesc('organizations');

        $payment_plans_list = $payplans->values()->all();
        view()->share('payment_plans_list', $payment_plans_list);

        $payment_method = ['stripe' => 'Stripe'];
        view()->share('payment_method', $payment_method);

        return;
    }

    public function subscriptionExpired()
    {
        return view('errors.subscribe');
    }
}
