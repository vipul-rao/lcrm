<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Qtemplate;
use App\Models\QtemplateProduct;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class QTemplateController extends Controller
{
    private $user;

    /**
     * Get all qtemplates
     *
     * @Get("/qtemplates")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "qtemplates": {
    {
    "id": 1,
    "quotation_template": "product name",
    "quotation_duration": "10",
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
        $qtemplates = Qtemplate::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->select('id', 'quotation_template', 'quotation_duration')->get()->toArray();

        return response()->json(['qtemplates' => $qtemplates], 200);
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
     * Post qtemplate
     *
     * @Post("/post_qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11","total":"10.00","tax_amount":"1.11","grand_total":"11.11"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_template' => $request->input('quotation_template'),
            'quotation_duration' => $request->input('quotation_duration'),
            'total' => $request->input('total'),
            'tax_amount' => $request->input('tax_amount'),
            'grand_total' => $request->input('grand_total'),
        );
        $rules = array(
            'quotation_template' => 'required',
            'quotation_duration' => "required",
            'total' => "required",
            'tax_amount' => "required",
            'grand_total' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $qtemplate = new Qtemplate($request->except('token'));
            $this->user->qtemplates()->save($qtemplate);

            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $qtemplateProduct = new QtemplateProduct();
                        $qtemplateProduct->qtemplate_id = $qtemplate->id;
                        $qtemplateProduct->product_id = $item;
                        $qtemplateProduct->product_name = $request->product_name[$key];
                        $qtemplateProduct->description = $request->description[$key];
                        $qtemplateProduct->quantity = $request->quantity[$key];
                        $qtemplateProduct->price = $request->price[$key];
                        $qtemplateProduct->sub_total = $request->sub_total[$key];
                        $qtemplateProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get qtemplate item
     *
     * @Get("/qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "qtemplate_id":"1"}),
     *       @Response(200, body={"qtemplate": {
    "id" : 1,
    "quotation_template" : "testaa",
    "quotation_duration" : 19,
    "immediate_payment" : 0,
    "terms_and_conditions" : "sd f sdf 22",
    "total" : 2553.0,
    "tax_amount" : 408.48,
    "grand_total" : 2961.48,
    "user_id" : 1,
    "created_at" : "2015-12-23 18:45:58",
    "updated_at" : "2015-12-23 18:46:21",
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
            'qtemplate_id' => $request->input('qtemplate_id'),
        );
        $rules = array(
            'qtemplate_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $qtemplate = Qtemplate::find($request->qtemplate_id);
            $products = array();
            if ($qtemplate->products->count() > 0) {
                foreach ($qtemplate->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * floatval(Settings::get('sales_tax')) / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['qtemplate' => $qtemplate->toArray(),'products'=>$products], 200);
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
     * Edit qtemplate
     *
     * @Post("/edit_qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "qtemplate_id":"1","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11","total":"10.00","tax_amount":"1.11","grand_total":"11.11"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'qtemplate_id' => $request->input('qtemplate_id'),
            'quotation_template' => $request->input('quotation_template'),
            'quotation_duration' => $request->input('quotation_duration'),
            'total' => $request->input('total'),
            'tax_amount' => $request->input('tax_amount'),
            'grand_total' => $request->input('grand_total'),
        );
        $rules = array(
            'qtemplate_id' => 'required',
            'quotation_template' => 'required',
            'quotation_duration' => "required",
            'total' => "required",
            'tax_amount' => "required",
            'grand_total' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $qtemplate = Qtemplate::find($request->qtemplate_id);
            $qtemplate->update($request->except('token','qtemplate_id'));

            QtemplateProduct::where('qtemplate_id', $qtemplate->id)->delete();
            
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $qtemplateProduct = new QtemplateProduct();
                        $qtemplateProduct->qtemplate_id = $qtemplate->id;
                        $qtemplateProduct->product_id = $item;
                        $qtemplateProduct->product_name = $request->product_name[$key];
                        $qtemplateProduct->description = $request->description[$key];
                        $qtemplateProduct->quantity = $request->quantity[$key];
                        $qtemplateProduct->price = $request->price[$key];
                        $qtemplateProduct->sub_total = $request->sub_total[$key];
                        $qtemplateProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete qtemplate
     *
     * @Post("/delete_qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "qtemplate_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'qtemplate_id' => $request->input('qtemplate_id'),
        );
        $rules = array(
            'qtemplate_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $qtemplate = Qtemplate::find($request->qtemplate_id);
            $qtemplate->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
