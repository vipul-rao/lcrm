<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use Yajra\Datatables\Datatables;

class InvoiceDeleteListController extends Controller
{
    private $invoiceRepository;
    protected $user;
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        parent::__construct();
        $this->invoiceRepository = $invoiceRepository;
        view()->share('type', 'invoice_delete_list');
    }
    public function index()
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $title = trans('invoice.delete_list');
        return view('user.invoice_delete_list.index',compact('title'));
    }

    public function show($invoice)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->invoiceDeleteList()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $title = trans('invoice.show_delete_list');
        $action = trans('action.show');
        return view('user.invoice_delete_list.show', compact('title', 'invoice','action'));
    }

    public function delete($invoice){
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->invoiceDeleteList()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $title = trans('invoice.restore_delete_list');
        $action = trans('action.restore');
        return view('user.invoice_delete_list.restore', compact('title', 'invoice','action'));
    }

    public function restoreInvoice($invoice)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.write'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $invoice = $this->invoiceRepository->invoiceDeleteList()->find($invoice);
        if (!$invoice){
            abort(404);
        }
        $invoice->update(['is_delete_list'=>0]);
        return redirect('invoice');
    }

    /**
     * @param Datatables $datatables
     * @return \Illuminate\Http\JsonResponse
     */
    public function data(Datatables $datatables)
    {
        $this->generateParams();
        if ((!$this->user->hasAccess(['invoices.read'])) && $this->user->orgRole=='staff') {
            return redirect('dashboard');
        }
        $orgRole = $this->getUser()->orgRole;
        $dateFormat = config('settings.date_format');
        $invoice = $this->invoiceRepository->invoiceDeleteList()
            ->map(function ($invoice) use ($orgRole, $dateFormat){
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'company_id' => isset($invoice->companies) ? $invoice->companies->name : '',
                    'invoice_date' => date($dateFormat, strtotime($invoice->invoice_date)),
                    'due_date' => date($dateFormat, strtotime($invoice->due_date)),
                    'final_price' => $invoice->final_price,
                    'unpaid_amount' => $invoice->unpaid_amount,
                    'status' => $invoice->status,
                    'payment_term' => $invoice->payment_term,
                    'count_payment' => $invoice->receivePayment->count(),
                    'orgRole' => $orgRole
                ];

            });

        return $datatables->collection($invoice)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term."",strtotime($due_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'invoice.invoice_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'invoice.invoice_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn(
                'actions',
                '
                                     @if(Sentinel::getUser()->hasAccess([\'invoices.read\']) || $orgRole=="admin")
                                     <a href="{{ url(\'invoice_delete_list/\' . $id . \'/show\' ) }}" title="{{ trans(\'table.details\') }}" >
                                            <i class="fa fa-fw fa-eye text-primary"></i> </a>                                 
                                    @endif
                                     @if((Sentinel::getUser()->hasAccess([\'invoices.write\']) || $orgRole=="admin") && $count_payment==0)
                                       <a href="{{ url(\'invoice_delete_list/\' . $id . \'/restore\' ) }}"  title="{{ trans(\'table.delete\') }}">
                                            <i class="fa fa-fw fa-undo text-success"></i> </a>
                                     @endif'
            )
            ->removeColumn('id')
            ->removeColumn('count_payment')
            ->removeColumn('payment_term')
            ->rawColumns(['actions'])
            ->escapeColumns( [ 'actions' ] )->make();
    }
    private function generateParams(){
        $this->user = $this->getUser();
    }
}
