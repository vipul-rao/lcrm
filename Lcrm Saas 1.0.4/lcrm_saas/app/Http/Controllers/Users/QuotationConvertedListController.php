<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\QuotationRepository;
use App\Repositories\SalesOrderRepository;
use Yajra\Datatables\Datatables;

class QuotationConvertedListController extends Controller
{
    private $quotationRepository;

    public $salesOrderRepository;

    public function __construct(QuotationRepository $quotationRepository,
                                SalesOrderRepository $salesOrderRepository)
    {
        parent::__construct();
        $this->quotationRepository = $quotationRepository;
        $this->salesOrderRepository = $salesOrderRepository;
        view()->share('type', 'quotation_converted_list');
    }

    public function index()
    {
        $title = trans('quotation.converted_list');
        return view('user.quotation.converted_list',compact('title'));
    }


    public function data(Datatables $datatables)
    {
        $orgRole = $this->getUser()->orgRole;
        $convertedList = $this->quotationRepository->quotationSalesOrderList()
            ->map(function ($convertedList) use ($orgRole){
                return [
                    'id' => $convertedList->id,
                    'quotations_number' => $convertedList->quotations_number,
                    'company_id' => $convertedList->companies->name ?? null,
                    'sales_team_id' => $convertedList->salesTeam->salesteam ?? null,
                    'final_price' => $convertedList->final_price,
                    'payment_term' => $convertedList->payment_term,
                    'status' => $convertedList->status,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($convertedList)
            ->addColumn('actions', '
                                            @if(Sentinel::getUser()->hasAccess([\'sales_orders.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation_converted_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                            @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    public function salesOrderList($id)
    {
        $salesorder_id = $this->salesOrderRepository->getAll()->where('quotation_id',$id)->first();
        if(isset($salesorder_id)){
            return redirect('sales_order/' . $salesorder_id->id . '/show');
        }else{
            flash(trans('quotation.sales_order_converted'))->error();
            return redirect('quotation_converted_list');
        }
    }
}
