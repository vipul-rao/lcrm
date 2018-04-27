<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use Yajra\Datatables\Datatables;

class InvoicePaidListController extends Controller
{
    private $invoiceRepository;
    public function __construct(InvoiceRepository $invoiceRepository)
    {
        parent::__construct();
        $this->invoiceRepository = $invoiceRepository;
        view()->share('type', 'paid_invoice');
    }

    public function index()
    {

        $title = trans('invoice.paid_invoice');
        return view('user.invoice.paid_invoice',compact('title'));
    }


    public function data(Datatables $datatables)
    {
        $dateFormat = config('settings.date_format');
        $paidList = $this->invoiceRepository->paidInvoice()
            ->map(function ($paidList) use ($dateFormat) {
                return [
                    'id' => $paidList->id,
                    'invoice_number' => $paidList->invoice_number,
                    'company_id' => $paidList->companies->name ?? null,
                    'invoice_date' => date($dateFormat, strtotime($paidList->invoice_date)),
                    'due_date' => date($dateFormat, strtotime($paidList->due_date)),
                    'final_price' => $paidList->final_price,
                    'unpaid_amount' => $paidList->unpaid_amount,
                    'status' => $paidList->status,
                    'payment_term' => isset($paidList->payment_term)?$paidList->payment_term:0,
                    'count_payment' => $paidList->receivePayment->count(),
                ];

            });

        return $datatables->collection($paidList)
            ->removeColumn('id')
            ->removeColumn('count_payment')
            ->removeColumn('payment_term')->make();
    }
}
