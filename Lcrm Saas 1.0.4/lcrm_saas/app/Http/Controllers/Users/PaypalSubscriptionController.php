<?php

namespace App\Http\Controllers\Users;

use App\Events\Subscription\PaypalSubscriptionCreated;
use App\Events\Subscription\ResumeSubscription;
use App\Events\Subscription\TrialWithoutCard;
use App\Http\Controllers\Controller;
use App\Repositories\InviteUserRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\PaypalTransactionRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Srmklive\PayPal\Facades\PayPal;

class PaypalSubscriptionController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    private $payPlanRepository;

    private $inviteUserRepository;

    private $subscriptionRepository;

    private $settingsRepository;

    private $paypalTransactionRepository;

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
        PaypalTransactionRepository $paypalTransactionRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->inviteUserRepository = $inviteUserRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->settingsRepository = $settingsRepository;
        $this->paypalTransactionRepository = $paypalTransactionRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;

        view()->share('type', 'paypal_subscription');
    }

    public function paypal($id){
        $payplan = $this->payPlanRepository->find($id);
        $provider = PayPal::setProvider('express_checkout');
        $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
        if (!isset($paypal_mode)){
            flash(trans('subscription.paypal_keys_are_required'))->error();
            return redirect()->back();
        }
        $subCount = $this->subscriptionRepository->all();
        $subCount = $subCount->count()+1;
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        if ($europian_tax=='true'){
            $countryCode = config('settings.country_code');
            $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
            if ($vat_number!=''){
                $taxRate = 0;
            }else{
                $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
            }
        }else{
            $taxRate = 0;
        }
        $amount = ($payplan->amount/100)+(($payplan->amount/100)*$taxRate);
        $now = now();
        $data = [];
        $data['items'] = [
            [
                'name' => "Subscription #{$subCount}",
                'price' => $amount,
                'qty' => 1
            ]
        ];
        $data['subscription_desc'] = "Subscription #{$subCount}";
        $data['invoice_id'] = $now;
        $data['invoice_description'] = "Subscription #{$subCount}";
        $data['return_url'] = url('payment/'.$id.'/paypal_success');
        $data['cancel_url'] = url('payment/'.$id.'/paypal_cancel');

        $total = 0;
        foreach($data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $data['total'] = $total;
        $response = $provider->setExpressCheckout($data,true);
        // if there is no link redirect back with error message
        if (!$response['paypal_link']) {
            return redirect('/')->with(['code' => 'danger', 'message' => 'Something went wrong with PayPal']);
            // For the actual error message dump out $response and see what's in there
        }

        // redirect to paypal
        // after payment is done paypal
        // will redirect us back to $this->expressCheckoutSuccess
        return redirect($response['paypal_link']);
    }

    public function paypalSuccess(Request $request,$id)
    {
        $organization = $this->userRepository->getOrganization();
        $payplan = $this->payPlanRepository->find($id);
        $subCount = $this->subscriptionRepository->all();
        $subCount = $subCount->count()+1;
        $now = now();
        $provider = PayPal::setProvider('express_checkout');
        $token = $request->token;
        $PayerID = $request->PayerID;
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        if ($europian_tax=='true'){
            $countryCode = config('settings.country_code');
            $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
            if ($vat_number!=''){
                $taxRate = 0;
            }else{
                $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
            }
        }else{
            $taxRate = 0;
        }
        $amount = ($payplan->amount/100)+(($payplan->amount/100)*$taxRate);
        $dataDo = [];
        $dataDo['items'] = [
            [
                'name' => "Subscription #{$subCount}",
                'price' => $amount,
                'qty' => 1
            ]
        ];

        $dataDo['subscription_desc'] = "Subscription #{$subCount}";
        $dataDo['invoice_id'] = $now;
        $dataDo['invoice_description'] = "Subscription #{$subCount}";

        $total = 0;
        foreach($dataDo['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $dataDo['total'] = $total;
        $provider->doExpressCheckoutPayment($dataDo, $token, $PayerID);

        $startdate = now()->toAtomString();
        $profile_desc = !empty($dataDo['subscription_desc']) ?
            $dataDo['subscription_desc'] : $dataDo['invoice_description'];
        if (empty($payplan->trial_period_days) || (isset($organization->subscriptions))){
            $data = [
                'PROFILESTARTDATE' => $startdate,
                'DESC' => $profile_desc,
                'BILLINGPERIOD' => ucfirst($payplan->interval), // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
                'BILLINGFREQUENCY' => $payplan->interval_count, //
                'AMT' => $amount, // Billing amount for each billing cycle
                'CURRENCYCODE' => strtoupper($payplan->currency), // Currency code
            ];
        }else{
            $data = [
                'PROFILESTARTDATE' => $startdate,
                'DESC' => $profile_desc,
                'BILLINGPERIOD' => ucfirst($payplan->interval), // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
                'BILLINGFREQUENCY' => $payplan->interval_count, //
                'AMT' => $amount, // Billing amount for each billing cycle
                'CURRENCYCODE' => strtoupper($payplan->currency), // Currency code
                'TRIALBILLINGPERIOD' => 'Day',  // (Optional) Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
                'TRIALBILLINGFREQUENCY' => $payplan->trial_period_days, // (Optional) set 12 for monthly, 52 for yearly
                'TRIALTOTALBILLINGCYCLES' => 1, // (Optional) Change it accordingly
                'TRIALAMT' => 0, // (Optional) Change it accordingly
            ];
        }

        $response = $provider->createRecurringPaymentsProfile($data, $token);
        if (!isset($response['PROFILEID'])){
            return $response;
        }
        $subscription = $this->subscriptionRepository->create([
            'organization_id' => $organization->id,
            'name' => $payplan->name,
            'profile_id' => $response['PROFILEID'],
            'subscription_type' => 'paypal',
            'quantity' => 1,
            'status' => 'Active',
            'payplan_id' => $payplan->id,
        ]);
        $organization->profile_id = $response['PROFILEID'];
        $organization->subscription_type = 'paypal';
        $organization->save();
        event(new PaypalSubscriptionCreated($subscription->id));
        return redirect('payment/status');
    }

    public function paypalCancelUrl()
    {
        return redirect('/');
    }

//    Without trial or skip trial
    public function paypalWithoutTrial($id){
        $payplan = $this->payPlanRepository->find($id);
        $provider = PayPal::setProvider('express_checkout');
        $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
        if (!isset($paypal_mode)){
            flash(trans('subscription.paypal_keys_are_required'))->error();
            return redirect()->back();
        }
        $subCount = $this->subscriptionRepository->all();
        $subCount = $subCount->count()+1;
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        if ($europian_tax=='true'){
            $countryCode = config('settings.country_code');
            $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
            if ($vat_number!=''){
                $taxRate = 0;
            }else{
                $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
            }
        }else{
            $taxRate = 0;
        }
        $amount = ($payplan->amount/100)+(($payplan->amount/100)*$taxRate);
        $now = now();
        $data = [];
        $data['items'] = [
            [
                'name' => "Subscription #{$subCount}",
                'price' => $amount,
                'qty' => 1
            ]
        ];
        $data['subscription_desc'] = "Subscription #{$subCount}";
        $data['invoice_id'] = $now;
        $data['invoice_description'] = "Subscription #{$subCount}";
        $data['return_url'] = url('change_generic_plan/'.$id.'/paypal_success');
        $data['cancel_url'] = url('change_generic_plan/'.$id.'/paypal_cancel');

        $total = 0;
        foreach($data['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $data['total'] = $total;
        $response = $provider->setExpressCheckout($data,true);
        // if there is no link redirect back with error message
        if (!$response['paypal_link']) {
            return redirect('/')->with(['code' => 'danger', 'message' => 'Something went wrong with PayPal']);
            // For the actual error message dump out $response and see what's in there
        }

        // redirect to paypal
        // after payment is done paypal
        // will redirect us back to $this->expressCheckoutSuccess
        return redirect($response['paypal_link']);
    }

    public function paypalSuccessWithoutTrial(Request $request,$id)
    {
        $organization = $this->userRepository->getOrganization();
        $payplan = $this->payPlanRepository->find($id);
        $subCount = $this->subscriptionRepository->all();
        $subCount = $subCount->count()+1;
        $now = now();
        $provider = PayPal::setProvider('express_checkout');
        $token = $request->token;
        $PayerID = $request->PayerID;
        $europian_tax = $this->settingsRepository->getKey('europian_tax');
        if ($europian_tax=='true'){
            $countryCode = config('settings.country_code');
            $vat_number = $this->organizationSettingsRepository->getKey('vat_number');
            if ($vat_number!=''){
                $taxRate = 0;
            }else{
                $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
            }
        }else{
            $taxRate = 0;
        }
        $amount = ($payplan->amount/100)+(($payplan->amount/100)*$taxRate);
        $dataDo = [];
        $dataDo['items'] = [
            [
                'name' => "Subscription #{$subCount}",
                'price' => $amount,
                'qty' => 1
            ]
        ];

        $dataDo['subscription_desc'] = "Subscription #{$subCount}";
        $dataDo['invoice_id'] = $now;
        $dataDo['invoice_description'] = "Subscription #{$subCount}";

        $total = 0;
        foreach($dataDo['items'] as $item) {
            $total += $item['price']*$item['qty'];
        }

        $dataDo['total'] = $total;
        $provider->doExpressCheckoutPayment($dataDo, $token, $PayerID);

        $startdate = now()->toAtomString();
        $profile_desc = !empty($dataDo['subscription_desc']) ?
            $dataDo['subscription_desc'] : $dataDo['invoice_description'];
        $data = [
            'PROFILESTARTDATE' => $startdate,
            'DESC' => $profile_desc,
            'BILLINGPERIOD' => ucfirst($payplan->interval), // Can be 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
            'BILLINGFREQUENCY' => $payplan->interval_count, //
            'AMT' => $amount, // Billing amount for each billing cycle
            'CURRENCYCODE' => strtoupper($payplan->currency), // Currency code
        ];

        $response = $provider->createRecurringPaymentsProfile($data, $token);
        if (!isset($response['PROFILEID'])){
            return $response;
        }
        $subscription = $this->subscriptionRepository->create([
            'organization_id' => $organization->id,
            'name' => $payplan->name,
            'profile_id' => $response['PROFILEID'],
            'subscription_type' => 'paypal',
            'quantity' => 1,
            'status' => 'Active',
            'payplan_id' => $payplan->id,
        ]);
        $organization->profile_id = $response['PROFILEID'];
        $organization->subscription_type = 'paypal';
        $organization->trial_ends_at = null;
        $organization->generic_trial_plan = null;
        $organization->created_by_admin = 0;
        $organization->save();
        event(new PaypalSubscriptionCreated($subscription->id));
        return redirect('payment/status');
    }

    public function paypalCancelUrlWithoutTrial()
    {
        return redirect('/');
    }

    public function paypalWithoutCard($id)
    {
        $organization = $this->userRepository->getOrganization();
        $payment = $this->payPlanRepository->find($id);
        $organization->trial_ends_at = now()->addDays($payment->trial_period_days);
        $organization->generic_trial_plan = $payment->id;
        $organization->subscription_type = 'paypal';
        $organization->save();
        event(new TrialWithoutCard($organization->id));
        return redirect('payment/success');
    }

    public function resumePaypalSubscription()
    {
        $organization = $this->userRepository->getOrganization();
        $subscription = $organization->subscriptions->first();
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
        event(new ResumeSubscription($subscription->id));
        flash(trans('subscription.resumed_successfully'), 'success');

        return redirect('subscription');
    }

    public function postNotify(Request $request){
        $subscription = $this->subscriptionRepository->findByField('profile_id',$request->recurring_payment_id)->last();
        if (isset($subscription)){
            if ($request->profile_status=='Cancelled'){
                $subscription->ends_at = now();
            }
            $subscription->status = $request->profile_status;
            $subscription->save();
        }
        info($request);
        if (isset($request['recurring_payment_id']) && $request['payment_status']=='Completed' && isset($subscription)){
            $this->paypalTransactionRepository->create([
                'organization_id' => $subscription->organization_id,
                'subscription_id' => $subscription->id,
                'txn_id' => $request['txn_id']
            ]);
        }
    }
    public function paymentActivity($id)
    {
        $title = trans('paypal_transaction.title');
        $provider = PayPal::setProvider('express_checkout');
        $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
        if (!isset($paypal_mode)){
            flash(trans('subscription.paypal_keys_are_required'))->error();
            return redirect()->back();
        }
        $paypalTransactions = $provider->getTransactionDetails($id);
        return view('user.subscription.transaction_details',compact('title','paypalTransactions'));
    }

    public function paypalTransactionInOrganization()
    {
        $title = trans('paypal_transaction.title');
        $provider = PayPal::setProvider('express_checkout');
        $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
        if (!isset($paypal_mode)){
            flash(trans('subscription.paypal_keys_are_required'))->error();
            return redirect()->back();
        }
        $organization = $this->userRepository->getOrganization();
        $transactions = $organization->paypalTransactions;
        $paypalTransactions = [];
        if (isset($transactions)){
            foreach ($transactions as $transaction){
                $paypalTransactions[] = $provider->getTransactionDetails($transaction->txn_id);
            }
            view()->share('paypalTransactions',$paypalTransactions);
        }
        return view('user.paypal_transaction.index',compact('title'));
    }
}
