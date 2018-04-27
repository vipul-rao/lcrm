<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\OrganizationRepository;
use App\Repositories\PayPlanRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Mpociot\VatCalculator\Facades\VatCalculator;
use Stripe\Charge;
use Stripe\Stripe;

class DashboardController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PayPlanRepository
     */
    private $payPlanRepository;
    /**
     * @var SubscriptionRepository
     */
    private $subscriptionRepository;
    private $organizationRepository;
    private $settingsRepository;

    /**
     * DashboardController constructor.
     *
     * @param UserRepository         $userRepository
     * @param PayPlanRepository      $payPlanRepository
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(
        UserRepository $userRepository,
        PayPlanRepository $payPlanRepository,
        SubscriptionRepository $subscriptionRepository,
        OrganizationRepository $organizationRepository,
        SettingsRepository $settingsRepository
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->payPlanRepository = $payPlanRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->settingsRepository = $settingsRepository;
    }

    public function index()
    {
        $organizations = $this->organizationRepository->all()->count();

        $payplans = $this->payPlanRepository->all()->count();
        $subscriptions = $this->subscriptionRepository->all()->count();
        $stripe_secret = $this->settingsRepository->getKey('stripe_secret');
        $data = [];

        if (isset($stripe_secret) && $stripe_secret) {
            try{
                Stripe::setApiKey($stripe_secret);
                $all_payments = Charge::all(['limit' => 100]);
                foreach (array_chunk($all_payments->data, 10) as $all_payment) {
                    foreach ($all_payment as $list) {
                        $created = $list->created;
                        $data[date('m', $created)][] = ($list->amount) / 100;
                    }
                }
            }catch (\Stripe\Error\Authentication $e){
                flash('Expired API Key provided.')->error();
            }
        }

        $graphics = [];

        for ($i = 11; $i >= 0; --$i) {
            $monthno = now()->subMonth($i)->format('m');
            $total_amount = isset($data[$monthno]) ? array_sum($data[$monthno]) : 0;
            $month = now()->subMonth($i)->format('M');
            $year = now()->subMonth($i)->format('Y');
            $graphics[] = [
                'month' => $month,
                'year' => $year,
                'organizations' => $this->organizationRepository->getMonthYear($monthno, $year)->count(),
                'payments_sum' => $total_amount,
                'subscriptions' => $this->subscriptionRepository->getSubscriptionsByMonthYear($monthno, $year)->count(),
            ];
        }

        return view('admin.index', compact('organizations', 'payplans', 'graphics', 'subscriptions'));
    }
}
