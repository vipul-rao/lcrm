<?php

namespace App\Http\Controllers\Users;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceMailRequest;
use App\Http\Requests\InvoiceRequest;
use App\Mail\SendQuotation;
use App\Repositories\CompanyRepository;
use App\Repositories\EmailRepository;
use App\Repositories\InvoicePaymentRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\ProductRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\QuotationTemplateRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use DataTables;
use Mpociot\VatCalculator\Facades\VatCalculator;

class InvoiceController extends Controller
{
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
     * @var QuotationRepository
     */
    private $quotationRepository;
    /**
     * @var SalesTeamRepository
     */
    private $salesTeamRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var QuotationTemplateRepository
     */
    private $quotationTemplateRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $emailRepository;
    private $organizationSettingsRepository;
    private $invoicePaymentRepository;
    protected $user;

    /**
     * InvoiceController constructor.
     *
     * @param CompanyRepository           $companyRepository
     * @param InvoiceRepository           $invoiceRepository
     * @param UserRepository              $userRepository
     * @param QuotationRepository         $quotationRepository
     * @param SalesTeamRepository         $salesTeamRepository
     * @param ProductRepository           $productRepository
     * @param QuotationTemplateRepository $quotationTemplateRepository
     * @param OptionRepository            $optionRepository
     */
    public function __construct(
        CompanyRepository $companyRepository,
        InvoiceRepository $invoiceRepository,
        UserRepository $userRepository,
        QuotationRepository $quotationRepository,
        SalesTeamRepository $salesTeamRepository,
        ProductRepository $productRepository,
        QuotationTemplateRepository $quotationTemplateRepository,
        OptionRepository $optionRepository,
        EmailRepository $emailRepository,
        OrganizationSettingsRepository $organizationSettingsRepository,
        InvoicePaymentRepository $invoicePaymentRepository
    ) {
        parent::__construct();

        $this->companyRepository = $companyRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->userRepository = $userRepository;
        $this->quotationRepository = $quotationRepository;
        $this->salesTeamRepository = $salesTeamRepository;
        $this->productRepository = $productRepository;
        $this->quotationTemplateRepository = $quotationTemplateRepository;
        $this->optionRepository = $optionRepository;
        $this->emailRepository = $emailRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        $this->invoicePaymentRepository = $invoicePaymentRepository;

        view()->share('type', 'invoice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('invoice.invoices');

        $this->invoicesData();

        return view('user.invoice.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $title = trans('invoice.new');

        return view('user.invoice.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param InvoiceRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        if (empty($request->qtemplate_id)) {
            $request->merge(['qtemplate_id' => 0]);
        }
        $invoice = $this->invoiceRepository->withAll()->count();
        if (0 == $invoice) {
            $total_fields = 0;
        } else {
            $total_fields = $this->invoiceRepository->withAll()->last()->id;
        }
        if($request->status == trans('invoice.paid_invoice')){
            $request->merge(['unpaid_amount' => 0]);
        }else{
            $request->merge(['unpaid_amount' => $request->final_price]);

        }
        $start_number = config('settings.invoice_start_number');
        $invoice_number = config('settings.invoice_prefix').((is_int($start_number) ? $start_number : 0) + (isset($total_fields) ? $total_fields : 0) + 1);
        $request->merge(['invoice_number' => $invoice_number, 'is_delete_list' => 0, 'order_id' => 0]);

        $this->invoiceRepository->createInvoice($request->all());

        return redirect('invoice');
    }

    public function edit($invoice)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->getAll()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $this->emailRecipients($invoice->company_id);
        $title = trans('invoice.edit').' '.$invoice->invoice_number;

        return view('user.invoice.edit', compact('title', 'invoice'));
    }

    public function update(InvoiceRequest $request, $invoice)
    {

        $invoice = $this->invoiceRepository->getAll()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $final_price = $invoice->final_price;
        $unpaid_amout = $invoice->unpaid_amount;
        if($request->final_price > $final_price){
            $unpaid_amout = $unpaid_amout + ( $request->final_price - $final_price );
        }else{
            $unpaid_amout = $unpaid_amout - ( $final_price - $request->final_price );
        }
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.write'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        if (empty($request->qtemplate_id)) {
            $request->merge(['qtemplate_id' => 0]);
        }
        if($request->status == trans('invoice.paid_invoice')){
            $request->merge(['unpaid_amount' => 0]);
        }else{
            $request->merge(['unpaid_amount' => $unpaid_amout]);
        }
        $this->invoiceRepository->updateInvoice($request->all(), $invoice->id);

        return redirect('invoice');
    }

    public function show($invoice)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->getAll()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $this->emailRecipients($invoice->company_id);
        $title = trans('invoice.show');
        $action = trans('action.show');

        return view('user.invoice.show', compact('title', 'invoice', 'action'));
    }

    public function delete($invoice)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->getAll()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $title = trans('invoice.delete');

        return view('user.invoice.delete', compact('title', 'invoice'));
    }

    public function destroy($invoice)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['invoices.delete'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->getAll()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $invoice->update(['is_delete_list' => 1]);

        return redirect('invoice');
    }

    public function data()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.read'])) && 'staff' == $this->user->orgRole) {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $invoices = $this->invoiceRepository->getAll()
            ->map(function ($invoice) use ($orgRole, $dateFormat){
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'company_id' => $invoice->companies->name ?? null,
                        'invoice_date' => date($dateFormat, strtotime($invoice->invoice_date)),
                        'due_date' => date($dateFormat, strtotime($invoice->due_date)),
                        'final_price' => $invoice->final_price,
                        'unpaid_amount' => $invoice->unpaid_amount,
                        'status' => $invoice->status,
                        'payment_term' => $invoice->payment_term,
                        'count_payment' => $invoice->receivePayment->count(),
                        'orgRole' => $orgRole,
                    ];
                }
            );

        return DataTables::of($invoices)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($due_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'invoice.invoice_expired\')}}"></i>
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'invoice.invoice_will_expire\')}}"></i>
                                     @endif'
            )
            ->addColumn(
                'actions',
                '@if(Sentinel::getUser()->hasAccess([\'invoices.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'invoice/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning"></i> </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'invoices.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'invoice/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     <a href="{{ url(\'invoice/\' . $id . \'/print_quot\' ) }}" title="{{ trans(\'table.print\') }}">
                                            <i class="fa fa-fw fa-print text-primary "></i>  </a>
                                    @endif
                                     @if((Sentinel::getUser()->hasAccess([\'invoices.delete\']) || $orgRole=="admin") && $count_payment==0)
                                        <a href="{{ url(\'invoice/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif'
            )
            ->removeColumn('id')
            ->removeColumn('count_payment')
            ->removeColumn('payment_term')
            ->rawColumns(['expired', 'actions'])
            ->make();
    }

    public function printQuot($invoice)
    {
        $invoice = $this->invoiceRepository->find($invoice);
        $invoice_template = config('settings.invoice_template');
        $filename = trans('invoice.invoice').'-'.$invoice->invoice_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('invoice_template.'.$invoice_template, compact('invoice'));

        return $pdf->download($filename.'.pdf');
    }

    public function ajaxCreatePdf($invoice)
    {
        $invoice = $this->invoiceRepository->find($invoice);
        $invoice_template = config('settings.invoice_template');

        $filename = trans('invoice.invoice').'-'.Str::slug($invoice->invoice_number);
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('invoice_template.'.$invoice_template, compact('invoice'));
        $pdf->save('./pdf/'.$filename.'.pdf');
        $pdf->stream();
        echo url('pdf/'.$filename.'.pdf');
    }

    /**
     * @param InvoiceMailRequest $request
     */
    public function sendInvoice(InvoiceMailRequest $request)
    {
        $email_subject = $request->email_subject;
        $to_company = $this->companyRepository->all()->where('id', $request->recipients);
        $email_body = $request->message_body;
        $message_body = Common::parse_template($email_body);
        $invoice_pdf = $request->invoice_pdf;

        $site_email = config('settings.site_email');
        if (!empty($to_company) && false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
            foreach ($to_company as $item) {
                if (false === !filter_var($item->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($item->email)->send(new SendQuotation([
                        'from' => $site_email,
                        'subject' => $email_subject,
                        'message_body' => $message_body,
                        'quotation_pdf' => $invoice_pdf
                    ]));
                }
                $this->emailRepository->create([
                    'assign_customer_id' => $item->id,
                    'from' => $this->userRepository->getOrganization()->id,
                    'to' => $item->email,
                    'subject' => $email_subject,
                    'message' => $message_body,
                ]);
            }
            echo '<div class="alert alert-success">'.trans('invoice.success').'</div>';
        } else {
            echo '<div class="alert alert-danger">'.trans('invoice.error').'</div>';
        }
    }

    private function generateParams()
    {
        $this->user = $this->getUser();

        $products = $this->productRepository->orderBy('id', 'desc')->getAll();

        $qtemplates = $this->quotationTemplateRepository->getAll()->pluck('quotation_template', 'id')->prepend(trans('quotation.select_template'), '');

        $salesteams = $this->salesTeamRepository->orderBy('id', 'asc')->getAll()
            ->pluck('salesteam', 'id')->prepend(trans('dashboard.select_sales_team'), '');

        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('quotation.company_id'), '');

        $payment_term1 = config('settings.payment_term1');
        $payment_term2 = config('settings.payment_term2');
        $payment_term3 = config('settings.payment_term3');

        view()->share('products', $products);
        view()->share('qtemplates', $qtemplates);
        view()->share('salesteams', $salesteams);
        view()->share('companies', $companies);
        view()->share('payment_term1', $payment_term1);
        view()->share('payment_term2', $payment_term2);
        view()->share('payment_term3', $payment_term3);

        /*=== === === ===*/
        $sales_tax = $this->organizationSettingsRepository->getKey('sales_tax');
        $europian_tax = $this->organizationSettingsRepository->getKey('europian_tax');
        if ($europian_tax=='true'){
            $countryCode = config('settings.country_code');
            $taxRate = VatCalculator::getTaxRateForLocation($countryCode);
        }else{
            $taxRate = 0;
        }
        view()->share('sales_tax', isset($sales_tax) ? floatval($sales_tax) : 1);
        view()->share('taxRate',$taxRate*100);
        /*=== === === ===*/
    }

    private function invoicesData()
    {
        $open_invoice_total = round($this->invoiceRepository->getAllOpen()->sum('unpaid_amount'), 3);
        $overdue_invoices_total = round($this->invoiceRepository->getAllOverdue()->sum('unpaid_amount'), 3);
        $paid_invoices_total = round($this->invoicePaymentRepository->getAll()->sum('payment_received'),3);
        $invoices_total_collection = round($this->invoiceRepository->getAll()->sum('final_price'), 3);

        view()->share('open_invoice_total', $open_invoice_total);
        view()->share('overdue_invoices_total', $overdue_invoices_total);
        view()->share('paid_invoices_total', $paid_invoices_total);
        view()->share('invoices_total_collection', $invoices_total_collection);
    }

    private function emailRecipients($company_id)
    {
        $email_recipients = $this->companyRepository->all()->where('id', $company_id)->pluck('name', 'id')->prepend(trans('quotation.company_id'), '');
        view()->share('email_recipients', $email_recipients);
    }
}
