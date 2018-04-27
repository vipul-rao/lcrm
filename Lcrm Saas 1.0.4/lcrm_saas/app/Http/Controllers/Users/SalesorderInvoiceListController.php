<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use App\Repositories\SalesOrderRepository;
use Yajra\Datatables\Datatables;

class SalesorderInvoiceListController extends Controller
{
    private $salesOrderRepository;
    private $invoiceRepository;
    public function __construct(SalesOrderRepository $salesOrderRepository,
                                InvoiceRepository $invoiceRepository)
    {
        parent::__construct();
        $this->salesOrderRepository = $salesOrderRepository;
        $this->invoiceRepository = $invoiceRepository;

        view()->share('type', 'salesorder_invoice_list');
    }

    public function index()
    {
        $title = trans('sales_order.salesorder_invoice_list');
        return view('user.sales_order.salesorder_invoice_list',compact('title'));
    }


    public function data(Datatables $datatables)
    {
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $salesOrderInvoiceList = $this->salesOrderRepository->onlySalesorderInvoiceLists()
            ->map(function ($salesOrderInvoiceList) use ($orgRole, $dateFormat){
                return [
                    'id' => $salesOrderInvoiceList->id,
                    'sale_number' => $salesOrderInvoiceList->sale_number,
                    'company_id' => $salesOrderInvoiceList->companies->name ?? null,
                    'sales_team_id' => $salesOrderInvoiceList->salesTeam->salesteam ?? null,
                    'final_price' => $salesOrderInvoiceList->final_price,
                    'date' => date($dateFormat, strtotime($salesOrderInvoiceList->date)),
                    'exp_date' => date($dateFormat, strtotime($salesOrderInvoiceList->exp_date)),
                    'payment_term' => $salesOrderInvoiceList->payment_term,
                    'status' => $salesOrderInvoiceList->status,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($salesOrderInvoiceList)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'invoices.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'salesorder_invoice_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    public function invoiceList($id)
    {
        $invoice_id = $this->invoiceRepository->getAll()->where('order_id',$id)->first();
        if(isset($invoice_id)){
            return redirect('invoice/' . $invoice_id->id . '/show');
        }else{
            return redirect('salesorder_invoice_list')->withErrors(trans('quotation.converted_invoice'));
        }
    }
}
