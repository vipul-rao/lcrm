<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AdminPayPlanRequest;
use App\Http\Controllers\Controller;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SubscriptionRepository;
use DataTables;
use App\Repositories\SettingsRepository;

class PayPlanController extends Controller
{
    /**
     * @var PayPlanRepository
     */
    private $payPlanRepository;

    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $settingsRepository;
    private $organizationRepository;
    private $subscriptionRepository;

    /**
     * PayPlanController constructor.
     *
     * @param PayPlanRepository $payPlanRepository
     * @param OptionRepository  $optionRepository
     */
    public function __construct(
        PayPlanRepository $payPlanRepository,
        OptionRepository $optionRepository,
        SettingsRepository $settingsRepository,
        OrganizationRepository $organizationRepository,
        SubscriptionRepository $subscriptionRepository
    ) {
        parent::__construct();

        $this->payPlanRepository = $payPlanRepository;
        $this->optionRepository = $optionRepository;
        $this->settingsRepository = $settingsRepository;
        $this->organizationRepository = $organizationRepository;
        $this->subscriptionRepository = $subscriptionRepository;

        view()->share('type', 'admin/payplan');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('payplan.payplans');

        return view('admin.payplan.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = trans('payplan.new_plan');

        $this->generateParams();

        return view('admin.payplan.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AdminPayPlanRequest $request)
    {
        if (empty($request->statement_descriptor)){
            $request->merge(['statement_descriptor'=>null]);
        }
        if (!isset($request->is_visible)) {
            $request->merge(['is_visible' => 0]);
        }
        if (empty($request->trial_period_days)) {
            $request->merge(['trial_period_days' => 0]);
        }
        $this->payPlanRepository->createPlan($request);

        return redirect('admin/payplan');
    }

    public function show($payplan)
    {
        $payplans = $this->payPlanRepository->all();
        $collection = collect($payplans);
        $sorted = $collection->sortByDesc('organizations');

        $payplans = $sorted->values()->all();
        $payplan = $this->payPlanRepository->find($payplan);

        $title = trans('payplan.show');

        $this->generateParams();
        $action = trans('action.show');

        $subscriptions_data = $this->subscriptionRepository->findByField('stripe_plan',$payplan->plan_id);
        $subscriptions_data_paypal = $this->subscriptionRepository->findByField('payplan_id',$payplan->id);
        $organizations_data = $this->organizationRepository->findByField('generic_trial_plan',$payplan->id);

        return view('admin.payplan.show', compact('title', 'payplan', 'action','subscriptions_data',
            'organizations_data','subscriptions_data_paypal'));
    }

    public function edit($payplan)
    {
        $payplan = $this->payPlanRepository->find($payplan);

        $title = trans('payplan.edit_plan');

        $this->generateParams();

        return view('admin.payplan.edit', compact('title', 'payplan'));
    }


    public function update(AdminPayPlanRequest $request, $payplan)
    {
        if (empty($request->statement_descriptor)){
            $request->merge(['statement_descriptor'=>null]);
        }
        if (!isset($request->is_visible)) {
            $request->merge(['is_visible' => 0]);
        }
        if (empty($request->trial_period_days)) {
            $request->merge(['trial_period_days' => 0]);
        }
        $plan = $this->payPlanRepository->find($payplan);
        $this->payPlanRepository->updatePlan($request, $plan);

        return redirect('admin/payplan');
    }

    /**
     * Get ajax datatables data.
     */
    public function data()
    {
        $plans = $this->payPlanRepository->orderBy('is_visible', 'desc')->all()
            ->map(function ($payPlan) {
                return [
                    'id' => $payPlan->id,
                    'name' => $payPlan->name,
                    'amount' => $payPlan->amount,
                    'currency' => $payPlan->currency,
                    'no_people' => 0 !== $payPlan->no_people ? $payPlan->no_people : '0 (Unlimited)',
                    'interval' => $payPlan->interval_count.' '.$payPlan->interval,
                    'trial_period_days' => $payPlan->trial_period_days ?? 'No Trial',
                    'is_visible' => isset($payPlan) && 1 == $payPlan->is_visible ? 'Visible' : 'Not Visible',
                    'organizations' => $payPlan->organizations,
                ];
            });

        return DataTables::of($plans)
            ->addColumn('actions', '<a href="{{ url(\'admin/payplan/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}"><i class="fa fa-fw fa-pencil text-warning"></i></a>
<a href="{{ url(\'admin/payplan/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.show\') }}"><i class="fa fa-fw fa-eye text-primary"></i></a>')
            ->rawColumns(['actions'])
            ->removeColumn('id')
            ->make();
    }

    private function generateParams()
    {
        $interval = $this->optionRepository->getAll()
            ->where('category', 'interval')
            ->map(
                function ($title) {
                    return [
                        'title' => $title->title,
                        'value' => $title->value,
                    ];
                }
            )->pluck('title', 'value')->prepend(trans('payplan.interval'), '');

        $currency = $this->optionRepository->getAll()
            ->where('category', 'currency')
            ->map(function ($title) {
                return [
                        'title' => $title->title,
                        'value' => $title->value,
                    ];
            })->pluck('title', 'value')->prepend(trans('payplan.currency'), '');

        view()->share('interval', $interval);
        view()->share('currency', $currency);
    }
}
