<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class CategoryController extends Controller
{
    private $user;

    /**
     * Get all categories
     *
     * @Get("/categories")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "category": {
    {
    "id": 1,
    "name": "Category name",
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
        $categories = Category::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            })->toArray();

        return response()->json(['categories' => $categories], 200);
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
     * Post category
     *
     * @Post("/post_category")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "name":"category name"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'name' => $request->input('name'),
        );
        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->categories()->create($request->except('token'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get category item
     *
     * @Get("/category")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "category_id":"1"}),
     *       @Response(200, body={"category": {
    "id" : 1,
    "name" : "Category",
    "user_id" : 1,
    "created_at" : "2015-12-23 16:58:25",
    "updated_at" : "2015-12-23 16:58:25",
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
            'category_id' => $request->input('category_id'),
        );
        $rules = array(
            'category_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $category = Category::find($request->category_id);
            return response()->json(['category' => $category->toArray()], 200);
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
     * Edit category
     *
     * @Post("/edit_category")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "category_id":"1","name":"category name"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'id' => $request->input('category_id'),
            'name' => $request->input('name'),
        );
        $rules = array(
            'id' => 'required',
            'name' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $category = Category::find($request->category_id);
            $category->update($request->except('token','category_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete category
     *
     * @Post("/delete_category")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "call_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $data = array(
            'id' => $request->input('category_id'),
        );
        $rules = array(
            'id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $category = Category::find($request->category_id);
            $category->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
