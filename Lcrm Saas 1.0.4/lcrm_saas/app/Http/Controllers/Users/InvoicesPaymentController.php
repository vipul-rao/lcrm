<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceReceivePaymentRequest;
use App\Repositories\CompanyRepository;
use App\Repositories\InvoicePaymentRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use DataTables;

class InvoicesPaymentController extends Controller
{
    /*user site settings*/
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

    private $organizationSettingsRepository;

    protected $user;

    public function __construct(
        InvoicePaymentRepository $invoicePaymentRepository,
        CompanyRepository $companyRepository,
        InvoiceRepository $invoiceRepository,
        UserRepository $userRepository,
        OptionRepository $optionRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        parent::__construct();

        $this->invoicePaymentRepository = $invoicePaymentRepository;
        $this->companyRepository = $companyRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->userRepository = $userRepository;
        $this->optionRepository = $optionRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        view()->share('type', 'invoices_payment_log');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('invoices_payment_log.invoices_payment_log');

        return view('user.invoices_payment_log.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('invoice.new');
        return view('user.invoices_payment_log.create', compact('title'));
    }

    public function store(InvoiceReceivePaymentRequest $request)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->find($request->invoice_id);
        $recive_payment = $this->invoicePaymentRepository->getAll()->count();

        if($recive_payment == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->invoicePaymentRepository->getAll()->last()->id;
        }

        $start_number = $this->organizationSettingsRepository->getKey('invoice_payment_start_number');
        $payment_no = $this->organizationSettingsRepository->getKey('invoice_payment_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);
        $request->merge(['payment_number'=> $payment_no,'company_id'=>$invoice->company_id]);
        $this->invoicePaymentRepository->createPayment($request->all());

        $unpaid_amount_new = round($invoice->unpaid_amount - $request->payment_received, 2);

        if ($unpaid_amount_new <= '0') {
            $invoice_data = [
                'unpaid_amount' => $unpaid_amount_new,
                'status' => trans('invoice.paid_invoice'),
            ];
        } else {
            $invoice_data = [
                'unpaid_amount' => $unpaid_amount_new,
            ];
        }

        $invoice->update($invoice_data);

        return redirect('invoices_payment_log');
    }

    public function show($invoiceReceivePayment)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $invoiceReceivePayment = $this->invoicePaymentRepository->find($invoiceReceivePayment);
        $title = trans('invoices_payment_log.show');
        $action = trans('action.show');

        return view('user.invoices_payment_log.show', compact('title', 'action', 'invoiceReceivePayment'));
    }

    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $dateTimeFormat = config('settings.date_time_format');
        $invoice_payments = $this->invoicePaymentRepository->getAll()
            ->map(function ($ip) use ($dateTimeFormat) {
                return [
                    'id' => $ip->id,
                    'payment_number' => $ip->payment_number,
                    'company_id' => $ip->invoice->companies->name ?? null,
                    'payment_received' => $ip->payment_received,
                    'invoice_number' => $ip->invoice->invoice_number ?? null,
                    'payment_method' => $ip->payment_method,
                    'payment_date' => date($dateTimeFormat, strtotime($ip->payment_date))
                ];
            });

        return DataTables::of($invoice_payments)
            ->addColumn('actions', '<a href="{{ url(\'invoices_payment_log/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}">
                                             <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->make();
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $invoices = $this->invoiceRepository->getAll()
            ->pluck('invoice_number', 'id')->prepend(trans('invoice.invoice_number'),'');

        $payment_methods = $this->optionRepository->getAll()
            ->where('category', 'payment_methods')
            ->map(function ($title) {
                return [
                    'text' => $title->title,
                    'id' => $title->value,
                ];
            })->pluck('text', 'id')->prepend(trans('invoice.payment_method'),'');

        view()->share('payment_methods', $payment_methods);
        view()->share('invoices',$invoices);
    }

    public function paymentLog(Request $request){
        $payment_details= $this->invoiceRepository->getAll()->where( 'id', $request->id )->first();
        return $payment_details;
    }
}
