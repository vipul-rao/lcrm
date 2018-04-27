<?php

namespace App\Http\Controllers\Users;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationMailRequest;
use App\Http\Requests\QuotationRequest;
use App\Mail\SendQuotation;
use App\Repositories\CompanyRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\EmailRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\ProductRepository;
use App\Repositories\QuotationRepository;
use App\Repositories\QuotationTemplateRepository;
use App\Repositories\SalesOrderRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use DataTables;
use Mpociot\VatCalculator\Facades\VatCalculator;

class QuotationController extends Controller
{
    /**
     * @var QuotationRepository
     */
    private $quotationRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SalesTeamRepository
     */
    private $salesTeamRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;
    /**
     * @var QuotationTemplateRepository
     */
    private $quotationTemplateRepository;
    /**
     * @var OptionRepository
     */
    private $optionRepository;

    private $organizationRepository;

    private $customerRepository;

    private $salesOrderRepository;

    private $invoiceRepository;

    private $emailRepository;
    private $organizationSettingsRepository;
    protected $user;


    /**
     * QuotationController constructor.
     *
     * @param QuotationRepository         $quotationRepository
     * @param UserRepository              $userRepository
     * @param SalesTeamRepository         $salesTeamRepository
     * @param ProductRepository           $productRepository
     * @param CompanyRepository           $companyRepository
     * @param QuotationTemplateRepository $quotationTemplateRepository
     * @param OptionRepository            $optionRepository
     */
    public function __construct(
        QuotationRepository $quotationRepository,
        UserRepository $userRepository,
        SalesTeamRepository $salesTeamRepository,
        ProductRepository $productRepository,
        CompanyRepository $companyRepository,
        QuotationTemplateRepository $quotationTemplateRepository,
        OptionRepository $optionRepository,
        OrganizationRepository $organizationRepository,
        CustomerRepository $customerRepository,
        SalesOrderRepository $salesOrderRepository,
        InvoiceRepository $invoiceRepository,
        EmailRepository $emailRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {
        $this->quotationRepository = $quotationRepository;
        $this->userRepository = $userRepository;
        $this->salesTeamRepository = $salesTeamRepository;
        $this->productRepository = $productRepository;
        $this->companyRepository = $companyRepository;
        $this->quotationTemplateRepository = $quotationTemplateRepository;
        $this->optionRepository = $optionRepository;
        $this->organizationRepository = $organizationRepository;
        $this->customerRepository = $customerRepository;
        $this->salesOrderRepository = $salesOrderRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->emailRepository = $emailRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        view()->share('type', 'quotation');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('quotation.quotations');

        $graphics = [];
        for ($i = 11; $i >= 0; --$i) {
            $monthno = now()->subMonth($i)->format('m');
            $month = now()->subMonth($i)->format('M');
            $year = now()->subMonth($i)->format('Y');
            $quotation = $this->quotationRepository->getMonthYear($monthno, $year);
            $graphics[] = [
                'month' => $month,
                'year' => $year,
                'send_quotation' => $quotation->where('is_delete_list',0)
                    ->where('is_converted_list',0)->where('is_quotation_invoice_list',0)->where('status','!=',trans('quotation.draft_quotation'))->count(),
                'draft_quotation' => $quotation->where('is_delete_list',0)->where('status','=',trans('quotation.draft_quotation'))->count(),
                'salesorder_list' => $quotation->where('is_converted_list',1)->count(),
                'invoice_list' => $quotation->where('is_quotation_invoice_list',1)->count(),
                'delete_list' => $quotation->where('is_delete_list',1)->count(),
            ];
        }

        return view('user.quotation.index', compact('title','graphics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('quotation.new');

        return view('user.quotation.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param QuotationRequest|Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(QuotationRequest $request)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        if(empty($request->qtemplate_id)){
            $request->merge(['qtemplate_id'=>0]);
        }
        $quotation = $this->quotationRepository->withAll()->count();;
        if($quotation == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->quotationRepository->withAll()->last()->id;
        }
        $start_number = config('settings.quotation_start_number');
        $quotation_no = config('settings.quotation_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);
        $request->merge(['quotations_number'=> $quotation_no,'is_delete_list'=>0,'is_converted_list'=>0,'is_quotation_invoice_list'=>0]);
        $this->quotationRepository->createQuotation($request->all());
        if ($request->status == trans('quotation.draft_quotation')){
            return redirect("quotation/draft_quotations");
        }else{
            return redirect("quotation");
        }
    }

    public function edit($quotation)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['quotations.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->findByField('status',trans('quotation.draft_quotation'))->find($quotation);
        if (!$quotation){
            abort(404);
        }
        $title = trans('quotation.edit');
        $this->emailRecipients($quotation->company_id);
        $main_staff = $this->salesTeamRepository->with('members')->find($quotation->sales_team_id)->members->pluck('full_name','id');

        return view('user.quotation.edit', compact('title', 'quotation','main_staff'));
    }

    public function update(QuotationRequest $request, $quotation)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        if(empty($request->qtemplate_id)){
            $request->merge(['qtemplate_id'=>0]);
        }
        $quotation_id = $quotation;
        $this->quotationRepository->updateQuotation($request->all(),$quotation_id);

        if ($request->status == trans('quotation.draft_quotation')){
            return redirect("quotation/draft_quotations");
        }else{
            return redirect("quotation");
        }
    }

    public function show($quotation)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->getAll()->find($quotation);
        if(!$quotation){
            abort(404);
        }
        $this->emailRecipients($quotation->company_id);
        $title = trans('quotation.show');
        $action = trans('action.show');

        return view('user.quotation.show', compact('title', 'quotation', 'action'));
    }

    public function delete($quotation)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->getAll()->find($quotation);
        if(!$quotation){
            abort(404);
        }
        $title = trans('quotation.delete');

        return view('user.quotation.delete', compact('title', 'quotation'));
    }

    public function destroy($quotation)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $quotation = $this->quotationRepository->getAll()->find($quotation);
        if(!$quotation){
            abort(404);
        }
        $quotation->update(['is_delete_list' => 1]);
        return redirect('quotation');
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['quotations.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $quotations = $this->quotationRepository->getAll()->where('status','!=',trans('quotation.draft_quotation'))->map(
                function ($quotation) use ($orgRole,$dateFormat){
                    return [
                        'id' => $quotation->id,
                        'quotations_number' => $quotation->quotations_number,
                        'company_id' => $quotation->companies->name ?? null,
                        'sales_team_id' => $quotation->salesTeam->salesteam ?? null,
                        'final_price' => $quotation->final_price,
                        'date' => date($dateFormat, strtotime($quotation->date)),
                        'exp_date' => date($dateFormat, strtotime($quotation->exp_date)),
                        'payment_term' => $quotation->payment_term,
                        'status' => $quotation->status,
                        'orgRole' => $orgRole
                    ];
                }
            );

        return DataTables::of($quotations)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($exp_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'quotation.quotation_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'quotation.quotation_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn(
                'actions',
                '@if(Sentinel::getUser()->hasAccess([\'quotations.write\']) && $status=="Draft Quotation" || $orgRole=="admin" && $status=="Draft Quotation")
                                    <a href="{{ url(\'quotation/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}" >
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'quotations.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     <a href="{{ url(\'quotation/\' . $id . \'/print_quot\' ) }}" title="{{ trans(\'table.print\') }}">
                                            <i class="fa fa-fw fa-print text-primary "></i>  </a>
                                    @endif
                                    @if(Sentinel::getUser()->hasAccess([\'sales_orders.write\']) && $status == \'Quotation Accepted\' || $orgRole=="admin" && $status == \'Quotation Accepted\' )
                                    <a href="{{ url(\'quotation/\' . $id . \'/confirm_sales_order\' ) }}" title="{{ trans(\'table.confirm_sales_order\') }}">
                                            <i class="fa fa-fw fa-check text-primary"></i> </a>
                                    @endif
                                     @if(Sentinel::getUser()->hasAccess([\'quotations.delete\']) || $orgRole=="admin")
                                   <a href="{{ url(\'quotation/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                   @endif'
            )
            ->removeColumn('id')
            ->rawColumns(['actions','expired'])
            ->make();
    }

    public function draftIndex(){
        $title=trans('quotation.draft_quotations');
        return view('user.quotation.draft_quotations', compact('title'));
    }

    public function draftQuotations()
    {
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $quotations = $this->quotationRepository->draftedQuotation()
            ->map(function ($quotation) use ($orgRole,$dateFormat) {
                return [
                    'id' => $quotation->id,
                    'quotations_number' => $quotation->quotations_number,
                    'company_id' => $quotation->companies->name ?? null,
                    'sales_team_id' => $quotation->salesTeam->salesteam ?? null,
                    'final_price' => $quotation->final_price,
                    'date' => date($dateFormat, strtotime($quotation->date)),
                    'exp_date' => date($dateFormat, strtotime($quotation->exp_date)),
                    'payment_term' => $quotation->payment_term,
                    'status' => $quotation->status,
                    'orgRole' => $orgRole
                ];
            });

        return DataTables::of($quotations)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($exp_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'quotation.quotation_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'quotation.quotation_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'quotations.write\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation/\' . $id . \'/edit\' ) }}" title="{{ trans(\'table.edit\') }}" >
                                            <i class="fa fa-fw fa-pencil text-warning"></i>  </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'quotations.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     <a href="{{ url(\'quotation/\' . $id . \'/print_quot\' ) }}" title="{{ trans(\'table.print\') }}">
                                            <i class="fa fa-fw fa-print text-primary "></i>  </a>
                                    @endif                                
                                     @if(Sentinel::getUser()->hasAccess([\'quotations.delete\']) || $orgRole=="admin")
                                   <a href="{{ url(\'quotation/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                   @endif')
            ->removeColumn('id')
            ->rawColumns(['actions','expired'])
            ->escapeColumns( [ 'actions' ] )->make();
    }

    public function confirmSalesOrder($quotation)
    {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $quotation = $this->quotationRepository->getAll()->find($quotation);
        if(!$quotation){
            abort(404);
        }

        $salesOrder = $this->salesOrderRepository->withAll()->count();;
        if($salesOrder == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->salesOrderRepository->withAll()->last()->id;
        }
        $start_number = config('settings.quotation_start_number');
        $sale_no = config('settings.sales_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);

        $saleorder = $this->salesOrderRepository->create([
            'sale_number' => $sale_no,
            'company_id' => $quotation->company_id,
            'date' => date(config('settings.date_format')),
            'exp_date' => $quotation->expire_date,
            'qtemplate_id' => $quotation->qtemplate_id,
            'payment_term' => isset($quotation->payment_term)?$quotation->payment_term:0,
            "sales_team_id" => $quotation->sales_team_id,
            'terms_and_conditions' => $quotation->terms_and_conditions,
            'total' => $quotation->total,
            'tax_amount' => $quotation->tax_amount,
            'vat_amount' => $quotation->vat_amount,
            'grand_total' => $quotation->grand_total,
            'discount' => $quotation->discount,
            'final_price' => $quotation->final_price,
            'status' => trans('sales_order.draft_salesorder'),
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'quotation_id' => $quotation->id,
            'is_delete_list' =>0,
            'is_invoice_list' =>0,
        ]);

        $list =[];
        if (!empty($quotation->quotationProducts->count() > 0)) {
            foreach ($quotation->quotationProducts as $key=>$item) {
                $temp['quantity']=$item->pivot->quantity;
                $temp['price']=$item->pivot->price;
                $list[$item->pivot->product_id]=$temp;
            }
        }
        $saleorder->salesOrderProducts()->attach($list);

        $quotation->update(['is_converted_list' => 1]);

        return redirect('sales_order/draft_salesorders');
    }

    public function makeInvoice($quotation)
    {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $quotation = $this->quotationRepository->getAll()->find($quotation);
        if(!$quotation){
            abort(404);
        }
        $invoice = $this->invoiceRepository->withAll()->count();
        if($invoice == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->invoiceRepository->withAll()->last()->id;
        }
        $start_number = config('settings.invoice_start_number');
        $invoice_number = config('settings.invoice_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);

        $invoice = $this->invoiceRepository->create([
            'quotation_id' => $quotation->id,
            'company_id' => $quotation->company_id,
            'sales_team_id' => $quotation->sales_team_id,
            'invoice_number' => $invoice_number,
            'invoice_date' => date(config('settings.date_format')),
            'due_date' => $quotation->expire_date,
            'payment_term' => $quotation->payment_term,
            'status' => trans('invoice.open_invoice'),
            'total' => $quotation->total,
            'vat_amount' => $quotation->vat_amount,
            'grand_total' => $quotation->grand_total,
            'unpaid_amount' => $quotation->final_price,
            'discount' => $quotation->discount,
            'final_price' => $quotation->final_price,
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'is_delete_list' =>0,
        ]);

        $list =[];
        if (!empty($quotation->quotationProducts->count() > 0)) {
            foreach ($quotation->quotationProducts as $key=>$item) {
                $temp['quantity']=$item->pivot->quantity;
                $temp['price']=$item->pivot->price;
                $list[$item->pivot->product_id]=$temp;
            }
        }
        $invoice->invoiceProducts()->attach($list);

        $quotation->update(['is_quotation_invoice_list' => 1]);
        return redirect('invoice');
    }


    public function ajaxQtemplatesProducts($qtemplate)
    {
        $qtemplateProduct = $this->quotationTemplateRepository->find($qtemplate);
        $templateProduct = [];
        foreach ($qtemplateProduct->qTemplateProducts as $product){
            $templateProduct[] = $product;
        }
        return $templateProduct;
    }

    public function printQuot($quotation)
    {
        $quotation = $this->quotationRepository->find($quotation);
        $quotation_template = config('settings.quotation_template');
        $this->generateParams();
        $filename = trans('quotation.quotation').'-'.$quotation->quotations_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('quotation_template.'.$quotation_template, compact('quotation'));

        return $pdf->download($filename.'.pdf');
    }

    public function ajaxCreatePdf($quotation)
    {
        $quotation = $this->quotationRepository->find($quotation);
        $quotation_template = config('settings.quotation_template');
        $filename = trans('quotation.quotation').'-'.$quotation->quotations_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('quotation_template.'.$quotation_template, compact('quotation'));
        $pdf->save('./pdf/'.$filename.'.pdf');
        $pdf->stream();
        echo url('pdf/'.$filename.'.pdf');
    }

    public function sendQuotation(QuotationMailRequest $request)
    {
        $email_subject = $request->email_subject;
        $to_company = $this->companyRepository->all()->where('id',$request->recipients);
        $email_body = $request->message_body;
        $message_body = Common::parse_template($email_body);
        $quotation_pdf = $request->quotation_pdf;

        $site_email = config('settings.site_email');


        if (!empty($to_company) && false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
            foreach ($to_company as $item) {
                if (false === !filter_var($item->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($item->email)->send(new SendQuotation([
                        'from' => $site_email,
                        'subject' => $email_subject,
                        'message_body' => $message_body,
                        'quotation_pdf' => $quotation_pdf
                    ]));
                }
                $this->emailRepository->create([
                    'assign_customer_id' => $item->id,
                    'from' => $this->userRepository->getOrganization()->id,
                    'to' => $item->email,
                    'subject' => $email_subject,
                    'message' => $message_body
                ]);
            }
            echo '<div class="alert alert-success">' . trans('quotation.success') . '</div>';
        } else {
            echo '<div class="alert alert-danger">' . trans('invoice.error') . '</div>';
        }
    }

    private function generateParams()
    {
        $this->user = $this->getUser();
        $products = $this->productRepository->orderBy('id', 'desc')->getAll();

        $qtemplates = $this->quotationTemplateRepository->getAll()->pluck('quotation_template', 'id')->prepend(trans('quotation.select_template'),'');

        $salesteams = $this->salesTeamRepository->orderBy('id', 'asc')->getAll()
                ->pluck('salesteam', 'id')->prepend(trans('dashboard.select_sales_team'),'');

        $companies = $this->companyRepository->orderBy('name', 'asc')->getAll()->pluck('name', 'id')->prepend(trans('quotation.company_id'),'');

        $payment_term1 = config('settings.payment_term1');
        $payment_term2 = config('settings.payment_term2');
        $payment_term3 = config('settings.payment_term3');

        view()->share('products', $products);
        view()->share('qtemplates', $qtemplates);
        view()->share('salesteams', $salesteams);
        view()->share('companies',$companies);
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
    }


    private function emailRecipients($company_id){
        $email_recipients = $this->companyRepository->all()->where('id',$company_id)->pluck('name','id')->prepend(trans('quotation.company_id'),'');
        view()->share('email_recipients', $email_recipients);
    }
}
