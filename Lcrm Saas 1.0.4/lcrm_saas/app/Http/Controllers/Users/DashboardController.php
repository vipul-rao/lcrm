<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OpportunityRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class DashboardController extends Controller
{
    /**
     * @var LeadRepository
     */
    private $leadRepository;
    /**
     * @var OpportunityRepository
     */
    private $opportunityRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;
    private $userRepository;
    private $organizationRepository;
    private $customerRepository;

    /**
     * DashboardController constructor.
     *
     * @param LeadRepository        $leadRepository
     * @param OpportunityRepository $opportunityRepository
     * @param UserRepository        $userRepository
     * @param CompanyRepository     $companyRepository
     * @param ProductRepository     $productRepository
     */
    public function __construct(LeadRepository $leadRepository,
                                OpportunityRepository $opportunityRepository,
                                UserRepository $userRepository,
                                CompanyRepository $companyRepository,
                                OrganizationRepository $organizationRepository,
                                CustomerRepository $customerRepository,
                                ProductRepository $productRepository)
    {
        parent::__construct();
        $this->leadRepository = $leadRepository;
        $this->opportunityRepository = $opportunityRepository;
        $this->companyRepository = $companyRepository;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->organizationRepository = $organizationRepository;
        $this->customerRepository = $customerRepository;
    }

    public function dashboard()
    {
        $customers = $this->customerRepository->getAll()->count();
        $opportunities = $this->opportunityRepository->getAll()->count();
        $products = $this->productRepository->getAll()->count();
        $organization = $this->userRepository->getOrganization();

        $opportunity_leads = [];

        for ($i = 11; $i >= 0; --$i) {
            $month = now()->subMonth($i)->format('M');
            $monthno = now()->subMonth($i)->format('m');
            $year = now()->subMonth($i)->format('Y');
            $opportunity_leads[] =
                ['month' => $month,
                    'year' => $year,
                    'opportunities' => $this->opportunityRepository->getMonthYear($monthno, $year)->count(),
                    'leads' => $this->leadRepository->getMonthYearWithUser($monthno, $year, $organization->id)->count(), ];
        }

        $customers_world = $this->companyRepository->getAll()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'latitude' => $customer->latitude,
                    'longitude' => $customer->longitude,
                    'city' => isset($customer->cities) ? $customer->cities->name : '',
                ];
            });
        $opportunity = $this->opportunityRepository->getAll();
        $opportunity_new = $opportunity->where('stages', 'New')->count();
        $opportunity_qualification = $opportunity->where('stages', 'Qualification')->count();
        $opportunity_proposition = $opportunity->where('stages', 'Proposition')->count();
        $opportunity_negotiation = $opportunity->where('stages', 'Negotiation')->count();
        $opportunity_won = $this->opportunityRepository->getConverted()->where('stages', 'Won')->count();
        $opportunity_loss = $this->opportunityRepository->getArchived()->where('stages', 'Loss')->count();

        $title = isset($organization) ? $organization->name : 'Lcrm Saas';

        view()->share('title', 'Welcome To '.$title);

        return view('user.index', compact('customers', 'opportunities','products',
            'customers_world', 'opportunity_leads', 'stages','opportunity_new','opportunity_qualification',
            'opportunity_proposition', 'opportunity_negotiation', 'opportunity_won', 'opportunity_loss'));
    }
}
