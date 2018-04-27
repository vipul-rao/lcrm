<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Repositories\InvoicePaymentRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\OrganizationSettingsRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use DataTables;
use Mpociot\VatCalculator\Facades\VatCalculator;

class InvoiceController extends Controller
{

    private $invoiceRepository;
    private $organizationSettingsRepository;
    private $invoicePaymentRepository;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrganizationSettingsRepository $organizationSettingsRepository,
        InvoicePaymentRepository $invoicePaymentRepository
    ) {
        parent::__construct();

        view()->share('type', 'customers/invoice');
        $this->invoiceRepository = $invoiceRepository;
        $this->organizationSettingsRepository = $organizationSettingsRepository;
        $this->invoicePaymentRepository = $invoicePaymentRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->generateParams();

        $title = trans('invoice.invoices');

        return view('customers.invoice.index', compact('title'));
    }

    public function show($invoice)
    {
        $this->generateParams();
        $invoice = $this->invoiceRepository->find($invoice);
        $title = trans('invoice.show').' '.$invoice->invoice_number;

        return view('customers.invoice.show', compact('title', 'invoice'));
    }

    public function data()
    {
        $company_id =$this->getUser()->customer->company->id;
        $dateFormat = config('settings.date_format');
        $invoices = $this->invoiceRepository->getAllForCustomer($company_id)
            ->map(function ($invoice) use ($dateFormat) {
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
                ];
            });

        return DataTables::of($invoices)
            ->addColumn(
                'expired',
                '@if(strtotime(date("m/d/Y"))>strtotime("+".$payment_term." ",strtotime($due_date)))
                                        <i class="fa fa-bell-slash text-danger" title="{{trans(\'invoice.invoice_expired\')}}"></i> 
                                     @else
                                      <i class="fa fa-bell text-warning" title="{{trans(\'invoice.invoice_will_expire\')}}"></i> 
                                     @endif'
            )
            ->addColumn('actions', '<a href="{{ url(\'customers/invoice/\' . $id . \'/show\' ) }}"  title={{ trans("table.details")}}>
                                            <i class="fa fa-fw fa-eye text-primary"></i>  </a>')
            ->removeColumn('id')
            ->removeColumn('count_payment')
            ->removeColumn('payment_term')
            ->rawColumns(['expired', 'actions'])
            ->make();
    }

    /**
     * @param Invoice $invoice
     *
     * @return mixed
     */
    public function printQuot($invoice)
    {
        $invoice = $this->invoiceRepository->find($invoice);
        $invoice_template = config('settings.invoice_template');
        $filename = trans('invoice.invoice').'-'.$invoice->invoice_number;
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('invoice_template.'.$invoice_template, compact('invoice'));

        return $pdf->download($filename.'.pdf');
    }


    public function ajaxCreatePdf($invoice)
    {
        $invoice = $this->invoiceRepository->find($invoice);
        $invoice_template = config('settings.invoice_template');

        $filename = trans('invoice.invoice').'-'.Str::slug($invoice->invoice_number);
        $pdf = App::make('dompdf.wrapper');
        $pdf->setPaper('a4','landscape');
        $pdf->loadView('invoice_template.'.$invoice_template, compact('invoice'));
        $pdf->save('./pdf/'.$filename.'.pdf');
        $pdf->stream();
        echo url('pdf/'.$filename.'.pdf');
    }
    private function generateParams()
    {
        $company_id =$this->getUser()->customer->company->id;
        $open_invoice_total = round($this->invoiceRepository->getAllOpenForCustomer($company_id)->sum('final_price'), 3);
        $overdue_invoices_total = round($this->invoiceRepository->getAllOverdueForCustomer($company_id)->sum('unpaid_amount'), 3);
        $paid_invoices_total = round($this->invoicePaymentRepository->getAllPaidForCustomer($company_id)->sum('payment_received'),3);
        $invoices_total_collection = round($this->invoiceRepository->getAllForCustomer($company_id)->sum('final_price'), 3);

        view()->share('open_invoice_total', $open_invoice_total);
        view()->share('overdue_invoices_total', $overdue_invoices_total);
        view()->share('paid_invoices_total', $paid_invoices_total);
        view()->share('invoices_total_collection', $invoices_total_collection);


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
}
