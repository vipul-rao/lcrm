<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Repositories\UserSettingRepository;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class InvoiceController extends Controller
{
    private $user;
    private $userSettingRepository;
    public function __construct(
        UserSettingRepository $userSettingRepository)
    {

        parent::__construct();
        $this->userSettingRepository = $userSettingRepository;
    }
    /**
     * Get all invoices
     *
     * @Get("/invoices")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "invoices": {
    {
    "id": 1,
    "invoice_number": "1465456",
    "invoice_date": "2015-11-11",
    "customer": "Customer Name",
    "unpaid_amount": "15.2",
    "status": "Status",
    "due_date": "2015-11-11",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoice::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'customer' => $invoice->customer->full_name,
                    'unpaid_amount' => $invoice->unpaid_amount,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date,
                ];
            })->toArray();

        return response()->json(['invoices' => $invoices], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Post invoice
     *
     * @Post("/post_invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "customer_id":"5", "invoice_date":"2015-11-11","sales_person_id":"2","status":"status","total":"10.00","tax_amount":"01.10","grand_total":"11.10","discount":1.2,"final_price":9.85,"invoice_prefix":"I00","invoice_start_number":"0"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'customer_id' => $request->input('customer_id'),
            'invoice_date' => $request->input('invoice_date'),
            'sales_person_id' => $request->input('sales_person_id'),
            'status' => $request->input('status'),
            'grand_total' => $request->input('grand_total'),
            'tax_amount' => $request->input('tax_amount'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
            'total' => $request->input('total'),
            'payment_term' => $request->input('payment_term'),
            'sales_team_id' => $request->input('sales_team_id'),
//            'invoice_prefix' => $request->input('invoice_prefix'),
//            'invoice_start_number' => $request->input('invoice_start_number'),
        );
        $rules = array(
            'customer_id' => 'required',
            'invoice_date' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'status' => 'required',
            'grand_total' => 'required',
            'tax_amount' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
            'total' => 'required',
            'payment_term' => "required",
//            'invoice_start_number' => "required",
//            'invoice_prefix' => "required"
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

//            $total_fields = Invoice::whereNull('deleted_at')->orWhereNotNull('deleted_at')->orderBy('id', 'desc')->first();
            $invoice_no = ((isset($total_fields) ? $total_fields->id : 0) + 1);
            $exp_date = date(Settings::get('date_format'), strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));


            $invoice = new Invoice($request->only('customer_id', 'invoice_date', 'payment_term',
                'sales_person_id', 'sales_team_id', 'status', 'total',
                'tax_amount', 'grand_total','final_price','discount'));
            $invoice->invoice_number = $invoice_no;
            $invoice->unpaid_amount = $request->grand_total;
            $invoice->due_date = isset($request->due_date) ? $request->due_date : strtotime($exp_date);
            $invoice->user_id = Sentinel::getUser()->id;
            $invoice->save();

            InvoiceProduct::where('invoice_id', $invoice->id)->delete();

            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $invoiceProduct = new InvoiceProduct();
                        $invoiceProduct->invoice_id = $invoice->id;
                        $invoiceProduct->product_id = $item;
                        $invoiceProduct->product_name = $request->product_name[$key];
                        $invoiceProduct->description = $request->description[$key];
                        $invoiceProduct->quantity = $request->quantity[$key];
                        $invoiceProduct->price = $request->price[$key];
                        $invoiceProduct->sub_total = $request->sub_total[$key];
                        $invoiceProduct->save();
                    }
                }
            }

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get invoice item
     *
     * @Get("/invoice")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "invoice_id":"1"}),
     *       @Response(200, body={"invoice": {
    "id" : 1,
    "order_id" : 0,
    "customer_id" : 3,
    "sales_person_id" : "2",
    "sales_team_id" : 1,
    "invoice_number" : "I0001",
    "invoice_date" : "08.12.2015. 00:00",
    "due_date" : "24.12.2015. 00:00",
    "payment_term" : "10",
    "status" : "Open Invoice",
    "total" : 1221.0,
    "tax_amount" : 195.36,
    "grand_total" : 1416.36,
    "discount" : 10,
    "final_price" : 1216.36,
    "unpaid_amount" : 1173.06,
    "user_id" : 1,
    "created_at" : "2015-12-23 18:05:35",
    "updated_at" : "2015-12-28 19:21:48",
    "deleted_at" : null,
    },"products": {
    "product" : "product",
    "description" : "description",
    "quantity" : 3,
    "unit_price" : 1.95,
    "taxes" : 1.55,
    "subtotal" : 195.36,
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function show(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'invoice_id' => $request->input('invoice_id'),
        );
        $rules = array(
            'invoice_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::where('id', $request->invoice_id)
                ->get()
                ->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'order_id' => $invoice->order_id,
                        'customer' => $invoice->customer->name,
                        'sales_person' => $invoice->salesPerson->full_name,
                        'salesteam' => $invoice->salesTeam->salesteam,
                        'invoice_number' => $invoice->invoice_number,
                        'invoice_date' => $invoice->invoice_date,
                        'due_date' => $invoice->due_date,
                        'payment_term' => $invoice->payment_term,
                        'status' => $invoice->status,
                        'total' => $invoice->total,
                        'tax_amount' => $invoice->tax_amount,
                        'grand_total' => $invoice->grand_total,
                        'discount' => $invoice->discount,
                        'final_price' => $invoice->final_price,
                        'unpaid_amount' => $invoice->unpaid_amount
                    ];
                });
            $products = array();
            $invoiceNew = Invoice::find($request->invoice_id);
            if ($invoiceNew->products->count() > 0) {
                foreach ($invoiceNew->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * floatval(Settings::get('sales_tax')) / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['invoice' => $invoice, 'products' => $products], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Edit invoice
     *
     * @Post("/edit_invoice")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "invoice_id":"1","customer_id":"5", "invoice_date":"2015-11-11","sales_person_id":"2","status":"status","total":"10.00","tax_total":"01.10","grand_total":"11.10","discount":"0.10","final_price":"9.10"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request, $id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'invoice_id' => $request->input('invoice_id'),
            'customer_id' => $request->input('customer_id'),
            'invoice_date' => $request->input('invoice_date'),
            'sales_person_id' => $request->input('sales_person_id'),
            'status' => $request->input('status'),
            'grand_total' => $request->input('grand_total'),
            'total' => $request->input('total'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
            'payment_term' => $request->input('payment_term'),
        );
        $rules = array(
            'invoice_id' => 'required',
            'customer_id' => 'required',
            'invoice_date' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'status' => 'required',
            'grand_total' => 'required',
            'total' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
            'payment_term' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::find($request->invoice_id);

            $exp_date = date(Settings::get('date_format'), strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));

            $payments = InvoiceReceivePayment::where('invoice_id', $invoice->id);

            $invoice->unpaid_amount = $request->grand_total - (($payments->count() > 0) ? $payments->sum('payment_received') : 0);
            $invoice->due_date = isset($request->due_date) ? $request->due_date : strtotime($exp_date);
            $invoice->update($request->only('customer_id', 'invoice_date', 'payment_term',
                'sales_person_id', 'sales_team_id', 'status', 'total','final_price','discount',
                'tax_amount', 'grand_total'));
            InvoiceProduct::where('invoice_id', $invoice->id)->delete();

            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $invoiceProduct = new InvoiceProduct();
                        $invoiceProduct->invoice_id = $invoice->id;
                        $invoiceProduct->product_id = $item;
                        $invoiceProduct->product_name = $request->product_name[$key];
                        $invoiceProduct->description = $request->description[$key];
                        $invoiceProduct->quantity = $request->quantity[$key];
                        $invoiceProduct->price = $request->price[$key];
                        $invoiceProduct->sub_total = $request->sub_total[$key];
                        $invoiceProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete invoice
     *
     * @Post("/delete_invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "invoice_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $data = array(
            'invoice_id' => $request->input('invoice_id'),
        );
        $rules = array(
            'invoice_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::find($request->invoice_id);
            $invoice->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
