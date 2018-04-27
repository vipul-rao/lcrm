<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Qtemplate;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class QuotationController extends Controller
{
    private $user;

    /**
     * Get all quotations
     *
     * @Get("/quotations")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "quotations": {
    {
    "id": 1,
    "quotations_number": "4545",
    "date": "2015-11-11",
    "customer": "customer name",
    "person": "person name",
    "final_price": "12",
    "status": "1",
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
        $quotations = Quotation::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('user', 'customer')
            ->get()
            ->map(function ($quotation) {
                return [
                    'id' => $quotation->id,
                    'quotations_number' => $quotation->quotations_number,
                    'date' => $quotation->date,
                    'customer' => $quotation->customer->full_name,
                    'person' => $quotation->user->full_name,
                    'final_price' => $quotation->final_price,
                    'status' => $quotation->status
                ];
            })->toArray();

        return response()->json(['quotations' => $quotations], 200);
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
     * Post quotation
     *
     * @Post("/post_quotation")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person_id":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25","quotation_prefix":"Q00","quotation_start_number":"0"}),
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
            'date' => $request->input('date'),
            'exp_date' => $request->input('exp_date'),
            'qtemplate_id' => $request->input('qtemplate_id'),
            'payment_term' => $request->input('payment_term'),
            'sales_person_id' => $request->input('sales_person_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'grand_total' => $request->input('grand_total'),
            'discount' => $request->input('discount'),
            'tax_amount' => $request->input('tax_amount'),
            'final_price' => $request->input('final_price'),
            'total' => $request->input('total'),
            'quotation_prefix' => $request->input('quotation_prefix'),
            'quotation_start_number' => $request->input('quotation_start_number'),
            'status' => $request->input('status'),
        );
        $rules = array(
            'customer_id' => 'required',
            'date' => 'required',
            'exp_date' => 'date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'tax_amount' => 'required',
            'final_price' => 'required',
            'total' => 'required',
            'quotation_prefix' => 'required',
            'quotation_start_number' => 'required',
            'status' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $total_fields = Quotation::whereNull('deleted_at')->orWhereNotNull('deleted_at')->orderBy('id', 'desc')->first();
            $quotation_no = $request->quotation_prefix . ($request->quotation_start_number + (isset($total_fields) ? $total_fields->id : 0) + 1);
            $exp_date = date(Settings::get('date_format'), strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));


            $quotation = new Quotation($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));
            $quotation->quotations_number = $quotation_no;
            $quotation->exp_date = isset($request->exp_date) ? $request->exp_date : strtotime($exp_date);
            $quotation->user_id = $this->user->id;
            $quotation->save();

            QuotationProduct::where('quotation_id', $quotation->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $quotationProduct = new QuotationProduct();
                        $quotationProduct->quotation_id = $quotation->id;
                        $quotationProduct->product_id = $item;
                        $quotationProduct->product_name = $request->product_name[$key];
                        $quotationProduct->description = $request->description[$key];
                        $quotationProduct->quantity = $request->quantity[$key];
                        $quotationProduct->price = $request->price[$key];
                        $quotationProduct->sub_total = $request->sub_total[$key];
                        $quotationProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get quotation item
     *
     * @Get("/quotation")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "quotation_id":"1"}),
     *       @Response(200, body={"quotation": {
    "id" : 1,
    "quotations_number" : "Q0001",
    "customer_id" : 3,
    "qtemplate_id" : 0,
    "date" : "08.12.2015. 00:00",
    "exp_date" : "30.12.2015.",
    "payment_term" : "10",
    "sales_person_id" : 2,
    "sales_team_id" : 1,
    "terms_and_conditions" : "dff dfg dfg",
    "status" : "Draft Quotation",
    "total" : 333.0,
    "tax_amount" : 53.28,
    "grand_total" : 386.28,
    "discount" : 11.28,
    "final_price" : 289.28,
    "user_id" : 1,
    "created_at" : "2015-12-23 18:39:12",
    "updated_at" : "2015-12-23 18:39:12",
    "deleted_at" : null
    },"products": {
    "product" : "product",
    "description" : "description",
    "quantity" : 3,
    "unit_price" : 1.95,
    "taxes" : 1.55,
    "subtotal" : 195.36
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function show(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_id' => $request->input('quotation_id'),
        );
        $rules = array(
            'quotation_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $quotation = Quotation::where('id', $request->quotation_id)
                ->get()
                ->map(function ($quotation) {
                    return [
                        'id' => $quotation->id,
                        'quotations_number' => $quotation->quotations_number,
                        'company' => $quotation->customer->full_name,
                        'qtemplate' => Qtemplate::find($quotation->qtemplate_id)->quotation_template,
                        'date' => $quotation->date,
                        'exp_date' => $quotation->exp_date,
                        'payment_term' => $quotation->payment_term,
                        'sales_person' => $quotation->salesPerson->full_name,
                        'salesteam' =>$quotation->salesTeam->salesteam,
                        'terms_and_conditions' => $quotation->terms_and_conditions,
                        'status' => $quotation->status,
                        'total' => $quotation->total,
                        'tax_amount' => $quotation->tax_amount,
                        'grand_total' => $quotation->grand_total,
                        'discount' => $quotation->discount,
                        'final_price' => $quotation->final_price
                    ];
                });
            $products = array();
            $quotationNew=Quotation::find($request->quotation_id);
            if ($quotationNew->products->count() > 0) {
                foreach ($quotationNew->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * floatval(Settings::get('sales_tax')) / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['quotation' => $quotation, 'products'=>$products], 200);
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
     * Edit quotation
     *
     * @Post("/edit_quotation")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "quotation_id":"1","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_id' => $request->input('quotation_id'),
            'customer_id' => $request->input('customer_id'),
            'date' => $request->input('date'),
            'exp_date' => $request->input('exp_date'),
            'qtemplate_id' => $request->input('qtemplate_id'),
            'payment_term' => $request->input('payment_term'),
            'sales_person_id' => $request->input('sales_person_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'grand_total' => $request->input('grand_total'),
            'discount' => $request->input('discount'),
            'tax_amount' => $request->input('tax_amount'),
            'final_price' => $request->input('final_price'),
            'total' => $request->input('total'),
            'quotation_prefix' => $request->input('quotation_prefix'),
            'quotation_start_number' => $request->input('quotation_start_number'),
            'status' => $request->input('status'),
        );
        $rules = array(
            'quotation_id' => 'required',
            'customer_id' => 'required',
            'exp_date' => 'date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'tax_amount' => 'required',
            'final_price' => 'required',
            'total' => 'required',
            'quotation_prefix' => 'required',
            'quotation_start_number' => 'required',
            'status' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $quotation = Quotation::find($request->quotation_id);

            $quotation->update($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));

            QuotationProduct::where('quotation_id', $quotation->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $quotationProduct = new QuotationProduct();
                        $quotationProduct->quotation_id = $quotation->id;
                        $quotationProduct->product_id = $item;
                        $quotationProduct->product_name = $request->product_name[$key];
                        $quotationProduct->description = $request->description[$key];
                        $quotationProduct->quantity = $request->quantity[$key];
                        $quotationProduct->price = $request->price[$key];
                        $quotationProduct->sub_total = $request->sub_total[$key];
                        $quotationProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete quotation
     *
     * @Post("/delete_quotation")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "quotation_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_id' => $request->input('quotation_id'),
        );
        $rules = array(
            'quotation_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $quotation = Quotation::find($request->quotation_id);
            $quotation->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
