<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\InviteUserRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Repositories\SettingsRepository;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use DataTables;

class PaymentController extends Controller
{
    private $subscriptionRepository;
    private $userRepository;
    private $settingsRepository;
    private $optionRepository;
    private $organizationRepository;
    private $payPlanRepository;
    private $inviteUserRepository;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository,
        OptionRepository $optionRepository,
        OrganizationRepository $organizationRepository,
        PayPlanRepository $payPlanRepository,
        InviteUserRepository $inviteUserRepository,
        SettingsRepository $settingsRepository
    ) {
        parent::__construct();
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository = $userRepository;
        $this->settingsRepository = $settingsRepository;
        $this->optionRepository = $optionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->inviteUserRepository = $inviteUserRepository;

        view()->share('type', 'admin/payment');
    }

    public function index()
    {
        $title = trans('payment.payments');

        return view('admin.payment.index', compact('title'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($payment)
    {
        $payment = $this->organizationRepository->organizationPayments()->find($payment);
        if(!isset($payment)){
            return redirect('admin/payment');
        }
        $title = trans('subscription.show_subscription');
        $action = trans('action.show');

        return view('admin.payment.show', compact('title', 'payment', 'action'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($payment)
    {
        $payment = $this->organizationRepository->organizationPayments()->find($payment);
        $unanswered_invites = $this->inviteUserRepository
            ->findWhere([
                'organization_id' => $payment->id,
                'claimed_at' => null,
            ])->count();
        if(!isset($payment)){
            return redirect('admin/payment');
        }
        $title = trans('organizations.edit_organization');
        $this->generateParams();
        return view('admin.payment.edit',compact('title','payment','unanswered_invites'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $payment)
    {
        $request->validate([
            'plan_id' => 'required',
            'duration' => 'required',
        ]);
        $payment = $this->organizationRepository->organizationPayments()->find($payment);
        if(!isset($payment)){
            return redirect('admin/payment');
        }
        $trial_ends_at = now()->gt($payment->trial_ends_at)?now()->addDays($request->duration):$payment->trial_ends_at->addDays($request->duration);
        $payment->update([
            'generic_trial_plan'=> $request->plan_id,
            'trial_ends_at' => $trial_ends_at,
        ]);
        return redirect('admin/payment');
    }

    public function data()
    {
        $dateTimeFormat = config('settings.date_time_format');
        $organization = $this->organizationRepository->organizationPayments()
            ->map(function ($organization) use ($dateTimeFormat){
                return [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'email' => $organization->email,
                    'generic_trial_plan' => $organization->genericPlan->name ?? null,
                    'trial_ends_at' => date($dateTimeFormat, strtotime($organization->trial_ends_at)),
                ];
            });

        return DataTables::of($organization)
            ->addColumn('actions', '<a href="{{ url(\'admin/payment/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     <a href="{{ url(\'admin/payment/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    private function generateParams()
    {
        $payplans = $this->payPlanRepository->all()->sortByDesc('organizations');

        $payment_plans_list = $payplans->values()->all();
        view()->share('payment_plans_list', $payment_plans_list);
    }
}
