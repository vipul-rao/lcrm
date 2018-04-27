<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuotationRepository;
use Yajra\Datatables\Datatables;

class QuotationInvoiceListController extends Controller
{
    private $quotationRepository;
    private $invoiceRepository;

    public function __construct(QuotationRepository $quotationRepository,
                                InvoiceRepository $invoiceRepository)
    {
        parent::__construct();
        $this->quotationRepository = $quotationRepository;
        $this->invoiceRepository = $invoiceRepository;
        view()->share('type', 'quotation_invoice_list');
    }

    public function index()
    {
        $title = trans('quotation.quotation_invoice_list');
        return view('user.quotation.quotation_invoice_list',compact('title'));
    }


    public function data(Datatables $datatables)
    {
        $orgRole = $this->getUser()->orgRole;
        $quotationInvoiceList = $this->quotationRepository->onlyQuotationInvoiceLists()
            ->map(function ($quotationInvoiceList) use ($orgRole){
                return [
                    'id' => $quotationInvoiceList->id,
                    'quotations_number' => $quotationInvoiceList->quotations_number,
                    'company_id' => $quotationInvoiceList->companies->name ?? null,
                    'sales_team_id' => $quotationInvoiceList->salesTeam->salesteam ?? null,
                    'final_price' => $quotationInvoiceList->final_price,
                    'payment_term' => $quotationInvoiceList->payment_term,
                    'status' => $quotationInvoiceList->status,
                    'orgRole' => $orgRole
                ];
            });

        return $datatables->collection($quotationInvoiceList)
            ->addColumn('actions', '@if(Sentinel::getUser()->hasAccess([\'invoices.read\']) || $orgRole=="admin")
                                    <a href="{{ url(\'quotation_invoice_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>@endif')
            ->removeColumn('id')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    public function invoiceList($id)
    {
        $invoice_id = $this->invoiceRepository->getAll()->where('quotation_id',$id)->last();
        if(isset($invoice_id)){
            return redirect('invoice/' . $invoice_id->id . '/show');
        }else{
            $invoice = $this->invoiceRepository->all()->where('quotation_id',$id)->first();
            if($invoice->is_delete_list==1){
                flash(trans('quotation.invoice_deleted'))->error();
            }else{
                flash(trans('quotation.converted_invoice'))->error();
            }
            return redirect('quotation_invoice_list');
        }
    }
}
