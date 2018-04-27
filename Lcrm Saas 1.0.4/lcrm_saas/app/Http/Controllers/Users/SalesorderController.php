<?php

namespace App\Http\Controllers\Users;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationMailRequest;
use App\Http\Requests\SaleorderRequest;
use App\Mail\SendQuotation;
use App\Repositories\CompanyRepository;
use App\Repositories\EmailRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\OrganizationSettingsRepository;
use App\Repositories\ProductRepository;
use App\Repositories\QuotationTemplateRepository;
use App\Repositories\SalesOrderRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use DataTables;
use Mpociot\VatCalculator\Facades\VatCalculator;

class SalesorderController extends Controller
{

    private $salesOrderRepository;
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

    private $invoiceRepository;
    private $emailRepository;
    private $organizationSettingsRepository;

    protected $user;

    public function __construct(
        SalesOrderRepository $salesOrderRepository,
        UserRepository $userRepository,
        SalesTeamRepository $salesTeamRepository,
        ProductRepository $productRepository,
        CompanyRepository $companyRepository,
        QuotationTemplateRepository $quotationTemplateRepository,
        OptionRepository $optionRepository,
        InvoiceRepository $invoiceRepository,
        EmailRepository $emailRepository,
        OrganizationSettingsRepository $organizationSettingsRepository
    ) {

        parent::__construct();

        $this->salesOrderRepository = $salesOrderRepository;
        $this->userRepository = $userRepository;
        $this->salesTeamRepository = $salesTeamRepository;
        $this->productRepository = $productRepository;
        $this->companyRepository = $companyRepository;
        $this->quotationTemplateRepository = $quotationTemplateRepository;
        $this->optionRepository = $optionRepository;
        $this->invoiceRepository = $invoiceRepository;
        $this->emailRepository = $emailRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;

        view()->share('type', 'sales_order');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('sales_order.sales_orders');

        $graphics = [];
        for ($i = 11; $i >= 0; --$i) {
            $monthno = now()->subMonth($i)->format('m');
            $month = now()->subMonth($i)->format('M');
            $year = now()->subMonth($i)->format('Y');
            $order = $this->salesOrderRepository->getMonthYear($monthno, $year);
            $graphics[] = [
                'month' => $month,
                'year' => $year,
                'send_salesorder' => $order->where('is_delete_list',0)
                    ->where('is_invoice_list',0)->where('status','!=',trans('sales_order.draft_salesorder'))->count(),
                'draft_salesorder' => $order->where('is_delete_list',0)->where('status','=',trans('sales_order.draft_salesorder'))->count(),
                'invoice_list' => $order->where('is_invoice_list',1)->count(),
                'delete_list' => $order->where('is_delete_list',1)->count(),
            ];
        }

        return view('user.sales_order.index', compact('title','graphics'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('sales_order.new');

        return view('user.sales_order.create', compact('title'));
    }

    public function store(SaleorderRequest $request)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        if(empty($request->qtemplate_id)){
            $request->merge(['qtemplate_id'=>0]);
        }
        $saleorder = $this->salesOrderRepository->withAll()->count();;
        if($saleorder == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->salesOrderRepository->withAll()->last()->id;
        }
        $start_number = config('settings.sales_start_number');
        $saleorder_no = config('settings.sales_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);
        $request->merge(['sale_number'=> $saleorder_no,'is_delete_list'=>0,'is_invoice_list'=>0]);
        $this->salesOrderRepository->createSalesOrder($request->all());

        if ($request->status == trans('sales_order.draft_salesorder')){
            return redirect("sales_order/draft_salesorders");
        }else{
            return redirect("sales_order");
        }
    }


    public function edit($saleorder)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->getAll()->find($saleorder);
        if (!$saleorder){
            abort(404);
        }
        $this->emailRecipients($saleorder->company_id);
        $title = trans('sales_order.edit');

        return view('user.sales_order.edit', compact('title', 'saleorder'));
    }

    public function update(SaleorderRequest $request, $saleorder)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        if(empty($request->qtemplate_id)){
            $request->merge(['qtemplate_id'=>0]);
        }
        $saleorder_id = $saleorder;
        $this->salesOrderRepository->updateSalesOrder($request->all(),$saleorder_id);

        if ($request->status == trans('sales_order.draft_salesorder')){
            return redirect("sales_order/draft_salesorders");
        }else{
            return redirect("sales_order");
        }
    }

    public function show($saleorder)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->find($saleorder);
        $this->emailRecipients($saleorder->company_id);
        $title = trans('sales_order.show');
        $action = trans('action.show');

        return view('user.sales_order.show', compact('title', 'saleorder', 'action'));
    }

    public function delete($saleorder)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->find($saleorder);
        $title = trans('sales_order.delete');
        return view('user.sales_order.delete', compact('title', 'saleorder'));
    }

    public function destroy($saleorder)
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.delete'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->find($saleorder);
        $saleorder->update(['is_delete_list' => 1]);
        return redirect('sales_order');
    }

    public function data()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $sales_order = $this->salesOrderRepository
            ->getAll()->where('status','!=',trans('sales_order.draft_salesorder'))->map(function ($saleOrder) use ($orgRole, $dateFormat){
                    return [
                        'id' => $saleOrder->id,
                        'sale_number' => $saleOrder->sale_number,
                        'company_id' => $saleOrder->companies->name ?? null,
                        'sales_team_id' => $saleOrder->salesTeam->salesteam ?? null,
                        'final_price' => $saleOrder->final_price,
                        'date' => date($dateFormat, strtotime($saleOrder->date)),
                        'exp_date' => date($dateFormat, strtotime($saleOrder->exp_date)),
                        'payment_term' => $saleOrder->payment_term,
                        'status' => $saleOrder->status,
                        'orgRole' => $orgRole
                    ];
                }
            );

        return DataTables::of($sales_order)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($exp_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'sales_order.salesorder_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'sales_order.salesorder_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn(
                'actions',
                '@if(Sentinel::getUser()->hasAccess([\'sales_orders.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'sales_order/\' . $id . \'/edit\' ) }}"  title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i>  </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'sales_orders.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'sales_order/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     <a href="{{ url(\'sales_order/\' . $id . \'/print_quot\' ) }}" title="{{ trans(\'table.print\') }}">
                                            <i class="fa fa-fw fa-print text-primary "></i>  </a>
                                    @endif
                                     @if(Sentinel::getUser()->hasAccess([\'sales_orders.delete\']) || $orgRole=="admin")
                                        <a href="{{ url(\'sales_order/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif'
            )
            ->removeColumn('id')
            ->rawColumns(['actions','expired'])
            ->make();
    }

    public function draftIndex(){
        $title=trans('sales_order.draft_salesorder');
        return view('user.sales_order.draft_salesorders', compact('title'));
    }
    public function draftSalesOrders()
    {
        $this->user = $this->getUser();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $sales_order = $this->salesOrderRepository->draftedSalesorder()
            ->map(function ($saleOrder) use ($orgRole, $dateFormat){
                    return [
                        'id' => $saleOrder->id,
                        'sale_number' => $saleOrder->sale_number,
                        'company_id' => $saleOrder->companies->name ?? null,
                        'sales_team_id' => $saleOrder->salesTeam->salesteam ?? null,
                        'final_price' => $saleOrder->final_price,
                        'date' => date($dateFormat, strtotime($saleOrder->date)),
                        'exp_date' => date($dateFormat, strtotime($saleOrder->exp_date)),
                        'payment_term' => $saleOrder->payment_term,
                        'status' => $saleOrder->status,
                        'orgRole' => $orgRole
                    ];
                }
            );

        return DataTables::of($sales_order)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($exp_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'sales_order.salesorder_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'sales_order.salesorder_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn(
                'actions',
                '@if(Sentinel::getUser()->hasAccess([\'sales_orders.write\']) || $orgRole=="admin")
                                        <a href="{{ url(\'sales_order/\' . $id . \'/edit\' ) }}"  title="{{ trans(\'table.edit\') }}">
                                            <i class="fa fa-fw fa-pencil text-warning "></i>  </a>
                                     @endif
                                     @if(Sentinel::getUser()->hasAccess([\'sales_orders.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'sales_order/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                     <a href="{{ url(\'sales_order/\' . $id . \'/print_quot\' ) }}" title="{{ trans(\'table.print\') }}">
                                            <i class="fa fa-fw fa-print text-primary "></i>  </a>
                                    @endif
                                     @if(Sentinel::getUser()->hasAccess([\'sales_orders.delete\']) || $orgRole=="admin")
                                        <a href="{{ url(\'sales_order/\' . $id . \'/delete\' ) }}" title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-trash text-danger"></i> </a>
                                     @endif'
            )
            ->removeColumn('id')
            ->rawColumns(['actions','expired'])
            ->make();
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

    public function printQuot($saleorder)
    {
        $saleorder = $this->salesOrderRepository->find($saleorder);
        $saleorder_template = config('settings.saleorder_template');
        $filename = trans('sales_order.sales_order').'-'.$saleorder->sale_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('saleorder_template.'.$saleorder_template, compact('saleorder'));

        return $pdf->download($filename.'.pdf');
    }

    public function ajaxCreatePdf($saleorder)
    {
        $saleorder = $this->salesOrderRepository->find($saleorder);
        $saleorder_template = config('settings.saleorder_template');
        $filename = trans('sales_order.sales_order').'-'.$saleorder->sale_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('saleorder_template.'.$saleorder_template, compact('saleorder'));
        $pdf->save('./pdf/'.$filename.'.pdf');
        $pdf->stream();
        echo url('pdf/'.$filename.'.pdf');
    }

    public function sendSaleorder(QuotationMailRequest $request)
    {
        $email_subject = $request->email_subject;
        $to_company = $this->companyRepository->all()->where('id',$request->recipients);
        $email_body = $request->message_body;
        $message_body = Common::parse_template($email_body);
        $saleorder_pdf = $request->saleorder_pdf;

        $site_email = config('settings.site_email');

        if (!empty($to_company) && false === !filter_var($site_email, FILTER_VALIDATE_EMAIL)) {
            foreach ($to_company as $item) {
                if (false === !filter_var($item->email, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($item->email)->send(new SendQuotation([
                        'from' => $site_email,
                        'subject' => $email_subject,
                        'message_body' => $message_body,
                        'quotation_pdf' => $saleorder_pdf
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
            echo '<div class="alert alert-success">' . trans('invoice.success') . '</div>';
        } else {
            echo '<div class="alert alert-danger">' . trans('invoice.error') . '</div>';
        }
    }

    public function makeInvoice($saleorder)
    {
        $user = $this->userRepository->getUser();
        $organization = $this->userRepository->getOrganization();

        $saleorder = $this->salesOrderRepository->getAll()->find($saleorder);
        $invoice = $this->invoiceRepository->withAll()->count();
        if($invoice == 0){
            $total_fields = 0;
        }else{
            $total_fields = $this->invoiceRepository->withAll()->last()->id;
        }
        $start_number = config('settings.invoice_start_number');
        $invoice_number = config('settings.invoice_prefix') . ((is_int($start_number)?$start_number:0) + (isset($total_fields) ? $total_fields : 0) + 1);
        $invoice = $this->invoiceRepository->create([
            'order_id' => $saleorder->id,
            'company_id' => $saleorder->company_id,
            'sales_team_id' => $saleorder->sales_team_id,
            'invoice_number' => $invoice_number,
            'invoice_date' => date(config('settings.date_format')),
            'due_date' => $saleorder->expire_date,
            'payment_term' => $saleorder->payment_term,
            'status' => trans('invoice.open_invoice'),
            'total' => $saleorder->total,
            'vat_amount' => $saleorder->vat_amount,
            'grand_total' => $saleorder->grand_total,
            'unpaid_amount' => $saleorder->final_price,
            'discount' => $saleorder->discount,
            'final_price' => $saleorder->final_price,
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'is_delete_list' =>0,
        ]);

        $list =[];
        if (!empty($saleorder->salesOrderProducts->count() > 0)) {
            foreach ($saleorder->salesOrderProducts as $key=>$item) {
                $temp['quantity']=$item->pivot->quantity;
                $temp['price']=$item->pivot->price;
                $list[$item->pivot->product_id]=$temp;
            }
        }
        $invoice->invoiceProducts()->attach($list);

        $saleorder->update(['is_invoice_list' => 1]);
        return redirect('invoice');
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
