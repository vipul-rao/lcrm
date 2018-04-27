<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use App\Repositories\SalesOrderRepository;
use Yajra\Datatables\Datatables;

class SalesorderDeleteListController extends Controller
{
    private $salesOrderRepository;
    private $invoiceRepository;
    protected $user;
    public function __construct(SalesOrderRepository $salesOrderRepository,
                                InvoiceRepository $invoiceRepository)
    {
        parent::__construct();
        $this->salesOrderRepository = $salesOrderRepository;
        $this->invoiceRepository = $invoiceRepository;
        view()->share('type', 'salesorder_delete_list');
    }
    public function index()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('sales_order.delete_list');
        return view('user.salesorder_delete_list.index',compact('title'));
    }

    public function show($saleorder)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->salesorderDeleteList()->find($saleorder);
        if (!$saleorder){
            abort(404);
        }
        $title = trans('sales_order.show_delete_list');
        $action = trans('action.show');
        return view('user.salesorder_delete_list.show', compact('title', 'saleorder','action'));
    }

    public function delete($saleorder){
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->salesorderDeleteList()->find($saleorder);
        $title = trans('sales_order.restore_delete_list');
        $action = 'delete';
        return view('user.salesorder_delete_list.restore', compact('title', 'saleorder','action'));
    }

    public function restoreSalesorder($saleorder)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $saleorder = $this->salesOrderRepository->salesorderDeleteList()->find($saleorder);
        $saleorder->update(['is_delete_list'=>0]);
        return redirect('sales_order');
    }

    public function data(Datatables $datatables)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['sales_orders.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $salesOrderDeleteList = $this->salesOrderRepository->salesorderDeleteList()
            ->map(function ($salesOrderDeleteList) use ($orgRole, $dateFormat){
                return [
                    'id' => $salesOrderDeleteList->id,
                    'sale_number' => $salesOrderDeleteList->sale_number,
                    'company_id' => isset($salesOrderDeleteList->companies) ? $salesOrderDeleteList->companies->name : '',
                    'sales_team_id' => isset($salesOrderDeleteList->salesTeam->salesteam)?$salesOrderDeleteList->salesTeam->salesteam:null,
                    'final_price' => $salesOrderDeleteList->final_price,
                    'date' => date($dateFormat, strtotime($salesOrderDeleteList->date)),
                    'exp_date' => date($dateFormat, strtotime($salesOrderDeleteList->exp_date)),
                    'payment_term' => $salesOrderDeleteList->payment_term,
                    'status' => $salesOrderDeleteList->status,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($salesOrderDeleteList)

            ->addColumn('actions', '
                                    <a href="{{ url(\'salesorder_delete_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>
                                    @if(Sentinel::getUser()->hasAccess([\'sales_orders.write\']) || $orgRole=="admin")
                                    <a href="{{ url(\'salesorder_delete_list/\' . $id . \'/restore\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-undo text-success"></i> </a>
                                       @endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    private function generateParams(){
        $this->user = $this->getUser();
    }
}
