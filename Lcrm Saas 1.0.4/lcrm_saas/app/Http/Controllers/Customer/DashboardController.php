<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\LeadRepository;
use App\Repositories\OptionRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\SalesOrderRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Mpociot\VatCalculator\Facades\VatCalculator;

class DashboardController extends Controller
{
    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;
    /**
     * @var QuotationRepository
     */
    private $quotationRepository;
    /**
     * @var SalesOrderRepository
     */
    private $salesOrderRepository;

    /**
     * @var LeadRepository
     */
    private $leadRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $userRepository;

    private $companyRepository;

    /**
     * DashboardController constructor.
     *
     * @param InvoiceRepository     $invoiceRepository
     * @param QuotationRepository   $quotationRepository
     * @param SalesOrderRepository  $salesOrderRepository
     * @param LeadRepository        $leadRepository
     * @param OptionRepository      $optionRepository
     */
    public function __construct(InvoiceRepository $invoiceRepository,
                                QuotationRepository $quotationRepository,
                                SalesOrderRepository $salesOrderRepository,
                                LeadRepository $leadRepository,
                                UserRepository $userRepository,
                                CompanyRepository $companyRepository,
                                OptionRepository $optionRepository)
    {
        parent::__construct();

        $this->invoiceRepository = $invoiceRepository;
        $this->quotationRepository = $quotationRepository;
        $this->salesOrderRepository = $salesOrderRepository;
        $this->leadRepository = $leadRepository;
        $this->optionRepository = $optionRepository;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company_id =$this->getUser()->customer->company->id;
        $this->generateParams();
        $data = [];

        for($i=11; $i>=0; $i--) {
            $monthno = Carbon::now()->subMonth($i)->format('m');
            $month = Carbon::now()->subMonth($i)->format('M');
            $year = Carbon::now()->subMonth($i)->format('Y');
            $data[] = [
                    'month' => $month,
                    'year' => $year,
                    'invoices' => $this->invoiceRepository->getInvoicesForCustomerByMonthYear($year, $monthno,$company_id)->sum('unpaid_amount'),
                    'quotations' => $this->quotationRepository->getQuotationsForCustomerByMonthYear($year, $monthno,$company_id)->count(),
                ];
        }

        $organization = $this->userRepository->getOrganization();
        $title = isset($organization)?$organization->name:'Saas';
        view()->share('title' , 'Welcome To '.$title);

        return view('customers.index', compact('data'));
    }

    private function generateParams()
    {
        $company_id =$this->getUser()->customer->company->id;
        $open_invoice_total = round($this->invoiceRepository->getAllOpenForCustomer($company_id)->sum('final_price'), 3);
        $paid_invoices_total = round($this->invoiceRepository->getAllPaidForCustomer($company_id)->sum('final_price'),3);
        $invoices_total_collection = round($this->invoiceRepository->getAllForCustomer($company_id)->sum('final_price'), 3);

        view()->share('open_invoice_total', $open_invoice_total);
        view()->share('paid_invoices_total', $paid_invoices_total);
        view()->share('invoices_total_collection', $invoices_total_collection);
        /*=== === === ===*/
    }
}
