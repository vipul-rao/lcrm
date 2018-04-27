<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Saleorder;
use App\Models\SaleorderProduct;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class SalesOrderController extends Controller
{
    private $user;
    /**
     * Get all sales orders
     *
     * @Get("/sales_orders")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "salesorders": {
    {
    "id": 1,
    "quotations_number": "product name",
    "date": "2015-11-11",
    "customer": "customer name",
    "person": "sales person name",
    "final_price": "12.53",
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
        $salesorder = Saleorder::whereHas('user', function ($q) {
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

        return response()->json(['salesorders' => $salesorder], 200);
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
     * Post Sales Order
     *
     * @Post("/post_sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person_id":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25","sales_prefix":"S00","sales_start_number":"0"}),
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
            "sales_prefix" => $request->input('sales_prefix'),
            "sales_start_number" => $request->input('sales_start_number'),
            'status' => $request->input('status'),
        );
        $rules = array(
            'customer_id' => 'required',
            'date' => 'required|date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'tax_amount' => 'required',
            'final_price' => 'required',
            'total' => 'required',
            'sales_prefix' => 'required',
            'sales_start_number' => 'required',
            'exp_date' => 'date',
            'status' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $total_fields = Saleorder::whereNull('deleted_at')->orWhereNotNull('deleted_at')->orderBy('id', 'desc')->first();
            $sale_no = $request->input('sales_prefix') . ($request->input('sales_start_number') + (isset($total_fields) ? $total_fields->id : 0) + 1);
            $exp_date = date(Settings::get('date_format'), strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));

            $saleorder = new Saleorder($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));
            $saleorder->sale_number = $sale_no;
            $saleorder->exp_date = isset($request->exp_date) ? $request->exp_date : strtotime($exp_date);
            $saleorder->user_id = $this->user->id;
            $saleorder->save();

            SaleorderProduct::where('order_id', $saleorder->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $saleorderProduct = new SaleorderProduct();
                        $saleorderProduct->order_id = $saleorder->id;
                        $saleorderProduct->product_id = $item;
                        $saleorderProduct->product_name = $request->product_name[$key];
                        $saleorderProduct->description = $request->description[$key];
                        $saleorderProduct->quantity = $request->quantity[$key];
                        $saleorderProduct->price = $request->price[$key];
                        $saleorderProduct->sub_total = $request->sub_total[$key];
                        $saleorderProduct->save();
                    }
                }
            }

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get salesorder item
     *
     * @Get("/salesorder")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "salesorder_id":"1"}),
     *       @Response(200, body={"salesorder": {
    "id" : 1,
    "sale_number" : "S0001",
    "customer_id" : 3,
    "qtemplate_id" : 0,
    "date" : "15.12.2015.",
    "exp_date" : "15.12.2015.",
    "payment_term" : "15",
    "sales_person_id" : 2,
    "sales_team_id" : 1,
    "terms_and_conditions" : "drtret",
    "status" : "Draft sales order",
    "total" : 1221.0,
    "tax_amount" : 195.36,
    "grand_total" : 1416.36,
    "discount" : 11.28,
    "final_price" : 289.28,
    "user_id" : 1,
    "created_at" : "2015-12-23 17:12:39",
    "updated_at" : "2015-12-23 17:12:39",
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
            'salesorder_id' => $request->input('salesorder_id'),
        );
        $rules = array(
            'salesorder_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $salesorder = Saleorder::where('id', $request->salesorder_id)
                ->get()
                ->map(function ($salesorder) {
                    return [
                        'id' => $salesorder->id,
                        'sale_number' => $salesorder->sale_number,
                        'customer' =>$salesorder->customer->full_name,
                        'date' => $salesorder->date,
                        'exp_date' => $salesorder->exp_date,
                        'payment_term' => $salesorder->payment_term,
                        'sales_person' => $salesorder->salesPerson->full_name,
                        'salesteam' => $salesorder->salesTeam->salesteam,
                        'terms_and_conditions' => $salesorder->terms_and_conditions,
                        'status' => $salesorder->status,
                        'total' => $salesorder->total,
                        'tax_amount' => $salesorder->tax_amount,
                        'grand_total' => $salesorder->grand_total,
                        'discount' => $salesorder->discount,
                        'final_price' => $salesorder->final_price,
                    ];
                });

            $products = array();
            $salesorderNew=Saleorder::find($request->salesorder_id);
            if ($salesorderNew->products->count() > 0) {
                foreach ($salesorderNew->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * floatval(Settings::get('sales_tax')) / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['salesorder' => $salesorder,'products' => $products], 200);
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
     * @Post("/edit_sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "sales_order_id":"1","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person_id":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'sales_order_id' => $request->input('sales_order_id'),
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
            "sales_prefix" => $request->input('sales_prefix'),
            "sales_start_number" => $request->input('sales_start_number'),
            'status' => $request->input('status'),
        );
        $rules = array(
            'sales_order_id' => 'required',
            'customer_id' => 'required',
            'date' => 'required|date',
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
            'sales_prefix' => 'required',
            'sales_start_number' => 'required',
            'status' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $sales_order = Saleorder::find($request->sales_order_id);
            $sales_order->update($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));

            SaleorderProduct::where('order_id', $sales_order->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $saleorderProduct = new SaleorderProduct();
                        $saleorderProduct->order_id = $sales_order->id;
                        $saleorderProduct->product_id = $item;
                        $saleorderProduct->product_name = $request->product_name[$key];
                        $saleorderProduct->description = $request->description[$key];
                        $saleorderProduct->quantity = $request->quantity[$key];
                        $saleorderProduct->price = $request->price[$key];
                        $saleorderProduct->sub_total = $request->sub_total[$key];
                        $saleorderProduct->save();
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
     * @Post("/delete_sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "sales_order_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'sales_order_id' => $request->input('sales_order_id'),
        );
        $rules = array(
            'sales_order_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $sales_order = Saleorder::find($request->sales_order_id);
            $sales_order->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
