<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class ProductController extends Controller
{
    private $user;
    /**
     * Get all products
     *
     * @Get("/products")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "products": {
    {
    "id": 1,
    "product_name": "product name",
    "name": "category",
    "product_type": "Type",
    "status": "1",
    "quantity_on_hand": "12",
    "quantity_available": "52"
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
        $products = Product::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('category')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'product_name' => $p->product_name,
                    'name' => $p->category->name,
                    'product_type' => $p->product_type,
                    'status' => $p->status,
                    'quantity_on_hand' => $p->quantity_on_hand,
                    'quantity_available' => $p->quantity_available,
                ];
            })->toArray();

        return response()->json(['products' => $products], 200);
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
     * Post product
     *
     * @Post("/post_product")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_name' => $request->input('product_name'),
            'sale_price' => $request->input('sale_price'),
            'description' => $request->input('description'),
            'product_type' => $request->input('product_type'),
            'status' => $request->input('status'),
            'category' => $request->input('category'),
            'quantity_on_hand' => $request->input('quantity_on_hand'),
            'quantity_available' => $request->input('quantity_available'),
        );
        $rules = array(
            'product_name' => "required",
            'sale_price' => "required",
            'description' => "required",
            'product_type' => "required",
            'status' => "required",
            'category' => "required",
            'quantity_on_hand' => "required",
            'quantity_available' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $product = new Product($request->except('token'));
            $this->user->products()->save($product);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get product item
     *
     * @Get("/product")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "product_id":"1"}),
     *       @Response(200, body={"product": {
    "id" : 1,
    "product_name" : "product",
    "product_image" : "",
    "category_id" : 1,
    "product_type" : "Consumable",
    "status" : "In Development",
    "quantity_on_hand" : 12,
    "quantity_available" : 22,
    "sale_price" : 1.0,
    "description" : "sdfdsfsdf",
    "description_for_quotations" : "sdfsdfsdfsdf",
    "user_id" : 1,
    "created_at" : "2015-12-23 16:58:51",
    "updated_at" : "2015-12-26 07:24:51",
    "deleted_at" : null
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function show(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_id' => $request->input('product_id'),
        );
        $rules = array(
            'product_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $product = Product::where('id', $request->product_id)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'product_name' => $product->product_name,
                        'product_image' =>$product->product_image,
                        'category_id' => $product->category->name,
                        'product_type' => $product->product_type,
                        'status' => $product->status,
                        'quantity_on_hand' => $product->quantity_on_hand,
                        'quantity_available' => $product->quantity_available,
                        'sale_price' => $product->sale_price,
                        'description' => $product->description,

                    ];
                });
            return response()->json(['product' => $product], 200);
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
     * Edit product
     *
     * @Post("/edit_product")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "product_id":"1","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_id' => $request->input('product_id'),
            'product_name' => $request->input('product_name'),
            'product_type' => $request->input('product_type'),
            'status' => $request->input('status'),
            'category' => $request->input('category'),
            'sale_price' => $request->input('sale_price'),
            'description' => $request->input('description'),
            'quantity_on_hand' => $request->input('quantity_on_hand'),
            'quantity_available' => $request->input('quantity_available'),
        );
        $rules = array(
            'product_id' => 'required',
            'product_name' => "required",
            'product_type' => "required",
            'status' => "required",
            'category' => "required",
            'sale_price' => "required",
            'description' => "required",
            'quantity_on_hand' => "required",
            'quantity_available' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $product = Product::find($request->product_id);
            $product->update($request->except('token','product_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete product
     *
     * @Post("/delete_product")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "product_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_id' => $request->input('product_id'),
        );
        $rules = array(
            'product_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $product = Product::find($request->product_id);
            $product->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
