<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepository;
use App\Repositories\EmailRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OptionRepository;
use App\Repositories\ProductRepository;
use App\Repositories\QuotationTemplateRepository;
use App\Repositories\SalesOrderRepository;
use App\Repositories\SalesTeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\App;
use DataTables;

class SalesorderController extends Controller
{

    private $salesOrderRepository;
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

    public function __construct(
        SalesOrderRepository $salesOrderRepository,
        UserRepository $userRepository,
        SalesTeamRepository $salesTeamRepository,
        ProductRepository $productRepository,
        CompanyRepository $companyRepository,
        QuotationTemplateRepository $quotationTemplateRepository,
        OptionRepository $optionRepository,
        InvoiceRepository $invoiceRepository,
        EmailRepository $emailRepository
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
        view()->share('type', 'customers/sales_order');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = trans('sales_order.sales_orders');
        return view('customers.sales_order.index', compact('title'));
    }


    public function show($saleorder)
    {
        $saleorder = $this->salesOrderRepository->getAll()->find($saleorder);
        $title = trans('quotation.show');

        return view('customers.sales_order.show', compact('title', 'saleorder'));
    }

    public function data()
    {
        $company_id =$this->getUser()->customer->company->id;
        $dateFormat = config('settings.date_format');
        $sales_orders = $this->salesOrderRepository->getAll()->where('company_id',$company_id)
            ->map(function ($saleOrder) use ($dateFormat){
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
                ];
            });

        return DataTables::of($sales_orders)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($exp_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'sales_order.salesorder_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'sales_order.salesorder_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn('actions', '<a href="{{ url(\'customers/sales_order/\' . $id . \'/show\' ) }}"  title="{{ trans(\'table.details\') }}">
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>')
            ->removeColumn('id')
            ->rawColumns(['actions','expired'])
            ->make();
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
}
