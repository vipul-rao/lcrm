<?php

namespace App\Http\Controllers\Admin;

use App\Events\Organization\OrganizationCreated;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SettingsRepository;
use DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationRolesRepository;
use App\Repositories\UserRepository;
use NotificationChannels\PusherPushNotifications\PusherMessage;
use Srmklive\PayPal\Facades\PayPal;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Event;
use Stripe\Stripe;

// @TODO:: Add appropriate flash messages
class OrganizationController extends Controller
{
    private $organizationRepository;

    private $userRepository;

    private $organizationRolesRepository;

    private $payPlanRepository;

    private $settingsRepository;

    private $organizationSettingsRepository;

    public function __construct(
        OrganizationRepository $organizationRepository,
        UserRepository $userRepository,
        OrganizationRolesRepository $organizationRolesRepository,
        PayPlanRepository $payPlanRepository,
        SettingsRepository $settingsRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
        ) {
        $this->organizationRepository = $organizationRepository;
        $this->userRepository = $userRepository;
        $this->organizationRolesRepository = $organizationRolesRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->settingsRepository = $settingsRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;

        view()->share('type', 'organizations');
    }

    public function index()
    {
        $title = trans('organizations.organizations');

        return view('admin.organizations.index', compact('title'));
    }

    public function data(DataTables $datatable)
    {
        $orgs = $this->organizationRepository->all()
            ->map(function ($orgs) {
                return [
                    'id' => $orgs->id,
                    'name' => $orgs->name,
                    'email' => $orgs->email,
                    'phone_number' => $orgs->phone_number,
                    'subscription_type' => $orgs->subscription_type??'--',
                    'is_deleted' => $orgs->is_deleted,
                ];
            });

        return DataTables::of($orgs)
            ->addColumn('actions', '<a href="{{ url(\'organizations/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     <a href="{{ url(\'organizations/\' . $id ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            @if($is_deleted==0)
                                                <a href="{{ url(\'organizations/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.disable\') }}">
                                                <i class="fa fa-fw fa-ban text-danger"></i> </a>
                                            @else
                                                <a href="{{ url(\'organizations/\' . $id . \'/activate\' ) }}" title="{{ trans(\'table.restore\') }}">
                                                <i class="fa fa-fw fa-undo text-success"></i> </a>
                                            @endif')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    public function create()
    {
        $title = trans('organizations.new_organization');
        $this->generateParams();

        return view('admin.organizations.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OrganizationRequest $request)
    {
        $this->user = $this->userRepository->getUser();
        $request->merge(['user_id' => $this->user->id]);

        $user = $this->userRepository->create([
            'first_name' => $request->owner_first_name,
            'last_name' => $request->owner_last_name,
            'email' => $request->owner_email,
            'phone_number' => $request->owner_phone_number,
            'password' => $request->owner_password,
            'user_id' => $request->user_id,
        ], true);

        $this->userRepository->assignRole($user, 'user');
        // create organization for the user
        $organization = '';
        if ($user) {
            // generate thumbnail
            if ($request->hasFile('organization_avatar_file')) {
                $file = $request->file('organization_avatar_file');
                $file = $this->organizationRepository->uploadLogo($file);

                $request->merge([
                    'logo' => $file->getFileInfo()->getFilename(),
                ]);
                $this->organizationRepository->generateThumbnail($file);
            }

            $organization = $this->organizationRepository->create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'user_id' => $user->id,
                'logo' => $request->logo,
                'trial_ends_at' => now()->addDays($request->duration),
                'generic_trial_plan' => $request->plan_id,
                'created_by_admin' => 1,
            ]);

            $request->merge([
                'site_name' => $request->name,
                'site_email' => $request->email,
                'phone' => $request->phone_number,
            ]);

            foreach ($request->only('site_name', 'site_email', 'phone') as $key => $value) {
                $this->organizationSettingsRepository->setKey($key, $value, $organization->id);
            }

            event(new OrganizationCreated($organization));
            $role = $this->organizationRolesRepository->findByField('slug', 'admin')->first();
            $this->organizationRolesRepository->attachRole($organization, $user, $role);
        }

        return redirect('organizations');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($org)
    {

        $organization = $this->organizationRepository->find($org);

        $title = trans('organizations.show_organization');
        $action = trans('action.show');
        $this->generateParams();

        if ($organization->subscription_type=='paypal'){
            $provider = PayPal::setProvider('express_checkout');
            $paypal_mode = $this->settingsRepository->getKey('paypal_mode');
            if (!isset($paypal_mode)){
                flash(trans('subscription.paypal_keys_are_required'))->error();
                return redirect()->back();
            }
            $transactions = $organization->paypalTransactions;
            $paypalTransactions = [];
            if (isset($transactions)){
                foreach ($transactions as $transaction){
                    $paypalTransactions[] = $provider->getTransactionDetails($transaction->txn_id);
                }
                view()->share('paypalTransactions',$paypalTransactions);
            }
        }

        $stripe_secret = $this->settingsRepository->getKey('stripe_secret');

        if (isset($stripe_secret) && $stripe_secret && isset($organization->stripe_id)) {
            Stripe::setApiKey($stripe_secret);
            $payments = Charge::all([
                'customer' => $organization->stripe_id,
                'limit' => 100,
            ]);
            $subscription_customerid = $organization->stripe_id;
            $subscription_customer = Customer::retrieve($subscription_customerid);
            $events = Event::all([
                'limit' => 100,
            ]);
            view()->share('events', $events);
            view()->share('subscription_customerid',$subscription_customerid);
            view()->share('subscription_customer', $subscription_customer);
            view()->share('payments', $payments);
        }

        return view('admin.organizations.show', compact('title', 'organization', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($org)
    {
        $organization = $this->organizationRepository->find($org);

        $user = $this->userRepository->find($organization->user_id);
        $this->generateParams();
        $organization->owner_first_name = $user->first_name;
        $organization->owner_last_name = $user->last_name;
        $organization->owner_phone_number = $user->phone_number;
        $organization->owner_email = $user->email;

        $title = trans('organizations.edit_organization');

        return view('admin.organizations.edit', compact('title', 'organization'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(OrganizationRequest $request, $org)
    {
        if ($request->hasFile('organization_avatar_file')) {
            $file = $request->file('organization_avatar_file');
            $file = $this->organizationRepository->uploadLogo($file);

            $request->merge([
                'logo' => $file->getFileInfo()->getFilename(),
            ]);
            $this->organizationRepository->generateThumbnail($file);
        }
        // Update Organization
        $organization = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ];
        if ($request->logo) {
            $organization['logo'] = $request->logo;
        }
        $this->organizationRepository->update($organization, $org);

        $request->merge([
            'site_name' => $request->name,
            'site_email' => $request->email,
            'phone' => $request->phone_number,
        ]);

        foreach ($request->only('site_name', 'site_email', 'phone') as $key => $value) {
            $this->organizationSettingsRepository->setKey($key, $value, $org);
        }

        // Update User
        $organization = $this->organizationRepository->find($org);

        $user = $this->userRepository->find($organization->user_id);

        $userData = [
            'first_name' => $request->owner_first_name,
            'last_name' => $request->owner_last_name,
            'email' => $request->owner_email,
            'phone_number' => $request->owner_phone_number,
        ];

        if ($request->owner_password) {
            $userData['password'] = bcrypt($request->owner_password);
        }

        $user = $this->userRepository->update($userData, $user->id);

        return redirect('organizations');
    }

    public function delete($org)
    {
        $organization = $this->organizationRepository->find($org);
        view()->share('title', trans('organizations.delete_organization'));

        return view('admin.organizations.delete', compact('organization'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($org)
    {
        $org = $this->organizationRepository->find($org);
        $org->update(['is_deleted' => 1]);

        return redirect('organizations');
    }

    public function activate($org)
    {
        $org = $this->organizationRepository->find($org);
        $org->update(['is_deleted' => 0]);

        return redirect('organizations');
    }

    private function generateParams()
    {
        $payplans = $this->payPlanRepository->all()->sortByDesc('organizations');

        $payment_plans_list = $payplans->values()->all();
        $org_payplan = $this->payPlanRepository->all();
        view()->share('payment_plans_list', $payment_plans_list);
        view()->share('org_payplan', $org_payplan);
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
        return view('admin.organizations.transaction_details',compact('title','paypalTransactions','active_subscription'));
    }
}
