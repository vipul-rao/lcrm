<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Repositories\InvoicePaymentRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\UserRepository;
use Yajra\Datatables\Datatables;

class InvoicesPaymentController extends Controller
{
    /**
     * @var InvoicePaymentRepository
     */
    private $invoicePaymentRepository;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var InvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    /**
     * @param InvoicePaymentRepository $invoicePaymentRepository
     * @param CompanyRepository $companyRepository
     * @param InvoiceRepository $invoiceRepository
     * @param UserRepository $userRepository
     * @param OptionRepository $optionRepository
     */
    public function __construct(InvoicePaymentRepository $invoicePaymentRepository,
                                CompanyRepository $companyRepository,
                                InvoiceRepository $invoiceRepository,
                                UserRepository $userRepository,
                                OptionRepository $optionRepository
    )
    {
        parent::__construct();

        $this->invoicePaymentRepository = $invoicePaymentRepository;
        $this->companyRepository = $companyRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->userRepository = $userRepository;
        $this->optionRepository = $optionRepository;
        view()->share('type', 'customers/invoices_payment_log');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('invoices_payment_log.invoices_payment_log');
        return view('customers.invoices_payment_log.index', compact('title'));
    }

    public function show($invoiceReceivePayment)
    {
        $invoiceReceivePayment = $this->invoicePaymentRepository->find($invoiceReceivePayment);
        $title = trans('invoices_payment_log.show');
        $action = trans('action.show');
        return view('customers.invoices_payment_log.show', compact('title', 'action','invoiceReceivePayment'));
    }


    public function data(Datatables $datatables)
    {
        $company_id =$this->getUser()->customer->company->id;
        $dateTimeFormat = config('settings.date_time_format');
        $invoice_payments = $this->invoicePaymentRepository->getAll()->where('company_id',$company_id)
            ->map(function ($ip) use ($dateTimeFormat){
                return [
                    'id' => $ip->id,
                    'payment_number' => $ip->payment_number,
                    'company_id' => isset($ip->invoice->companies->name)?$ip->invoice->companies->name:null,
                    'payment_received' => $ip->payment_received,
                    'invoice_number' => isset($ip->invoice) ? $ip->invoice->invoice_number : '',
                    'payment_method' => $ip->payment_method,
                    'payment_date' => date($dateTimeFormat, strtotime($ip->payment_date))
                ];
            });

        return $datatables->collection($invoice_payments)
            ->addColumn('actions', '<a href="{{ url(\'customers/invoices_payment_log/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
}
