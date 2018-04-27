<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceReceivePayment;
use App\Repositories\InvoiceRepository;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use JWTAuth;

class InvoicePaymentController extends Controller
{
    private $user;
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        parent::__construct();

        $this->middleware('authorized:contacts.read', ['only' => ['index', 'data']]);
        $this->middleware('authorized:contacts.write', ['only' => ['create', 'store', 'update', 'edit']]);
        $this->middleware('authorized:contacts.delete', ['only' => ['delete']]);

        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Get all invoice_payment.
     *
     * @Get("/invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
     * "invoices": {
     *  {
     *      "id": 1,
     *      "payment_number": "P002",
     *      "payment_received": "1525.26",
     *      "payment_method": "Paypal",
     *      "payment_date": "2015-11-11",
     *      "customer": "Customer Name",
     *      "person": "Person Name"
     *  }
     * }
     * }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $invoice_payments = InvoiceReceivePayment::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->with('invoice.customer', 'invoice.salesPerson')
            ->get()->map(function ($ip) {
                return [
                    'id' => $ip->id,
                    'payment_number' => $ip->payment_number,
                    'payment_received' => $ip->payment_received,
                    'invoice_number' => $ip->invoice->invoice_number,
                    'payment_method' => $ip->payment_method,
                    'payment_date' => $ip->payment_date,
                    'customer' => $ip->invoice->customer->full_name,
                    'salesperson' => $ip->invoice->salesPerson->full_name,
                ];
            })->toArray();

        return response()->json(['invoice_payments' => $invoice_payments], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Post invoice_payment.
     *
     * @Post("/post_invoice_payment")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "invoice_id":"5", "payment_date":"2015-11-11","payment_method":"2","payment_received":"555"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = [
            'invoice_id' => $request->input('invoice_id'),
            'payment_date' => $request->input('payment_date'),
            'payment_method' => $request->input('payment_method'),
            'payment_received' => $request->input('payment_received'),
        ];
        $rules = [
            'invoice_id' => 'required',
            'payment_date' => 'required',
            'payment_method' => 'required',
            'payment_received' => 'required',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::find($request->invoice_id);

            $total_fields = InvoiceReceivePayment::orderBy('id', 'desc')->first();

            $quotation_no = Settings::get('invoice_payment_prefix').(Settings::get('invoice_payment_start_number') + (isset($total_fields) ? $total_fields->id : 0) + 1);

            $payment_date = date(Settings::get('date_format'), strtotime(' + '.$request->payment_date));

            $invoiceRepository = $this->invoiceRepository->create($request->except('token', 'invoice_id'));
            $invoiceRepository->invoice()->associate($invoice);
            $invoiceRepository->payment_number = $quotation_no;
            $invoiceRepository->payment_date = isset($request->payment_date) ? $request->payment_date : strtotime($payment_date);
            $invoiceRepository->save();

            $unpaid_amount_new = bcsub($invoice->unpaid_amount, $request->payment_received, 2);

            if ($unpaid_amount_new <= '0') {
                $invoice_data = [
                    'unpaid_amount' => $unpaid_amount_new,
                    'status' => 'Paid Invoice',
                ];
            } else {
                $invoice_data = [
                    'unpaid_amount' => $unpaid_amount_new,
                ];
            }

            $invoice->update($invoice_data);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Get invoice_payment item.
     *
     * @Get("/invoice_payment")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
     * "invoice_payment": {
     *  {
     *      "id": 1,
     *      "payment_number": "P002",
     *      "payment_received": "1525.26",
     *      "payment_method": "Paypal",
     *      "payment_date": "2015-11-11",
     *      "customer": "Customer Name",
     *      "person": "Person Name"
     *  }
     * }
     * }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function show(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $invoice_payment = InvoiceReceivePayment::where('id', $request->invoice_payment_id)
            ->with('invoice.customer', 'invoice.salesPerson')
            ->get()
            ->map(function ($invoice_payment) {
                return [
                    'id' => $invoice_payment->id,
                    'payment_number' => $invoice_payment->payment_number,
                    'payment_received' => $invoice_payment->payment_received,
                    'invoice_number' => $invoice_payment->invoice->invoice_number,
                    'payment_method' => $invoice_payment->payment_method,
                    'payment_date' => $invoice_payment->payment_date,
                    'customer' => $invoice_payment->invoice->customer->full_name,
                    'salesperson' => $invoice_payment->invoice->salesPerson->full_name,
                ];
            });

        return response()->json(['invoice_payment' => $invoice_payment], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
