<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class CompanyController extends Controller
{
    private $user;

    /**
     * Get all companies
     *
     * @Get("/companies")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "companies": {
    {
    "id": 1,
    "name": "Name",
    "customer": "customer name",
    "phone": "634654165456",
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
        $companies = Company::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->latest()->get()->map(function ($comp) {
            return [
                'id' => $comp->id,
                'name' => $comp->name,
                'customer' => $comp->user->full_name,
                'phone' => $comp->phone,
            ];
        })->toArray();

        return response()->json(['companies' => $companies], 200);
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
     * Post company
     *
     * @Post("/post_company")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "name":"Company name","email":"email@email.com"}),
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
            'email' => $request->input('email'),
        );
        $rules = array(
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->companies()->create($request->except('token'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get company item
     *
     * @Post("/company")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "company_id":"1"}),
     *       @Response(200, body={"company": {
    "id" : 2,
    "name" : "dg dfg",
    "email" : "user@crm.com",
    "password" : "",
    "lostpw" : "",
    "address" : "fdgdfg",
    "website" : "gfdgfdg",
    "phone" : "45454",
    "mobile" : "45",
    "fax" : "4545",
    "title" : "",
    "company_avatar" : "",
    "company_attachment" : "",
    "main_contact_person" : 3,
    "sales_team_id" : 1,
    "country_id" : 1,
    "state_id" : 43,
    "city_id" : 5914,
    "longitude" : "63.30929400000002",
    "latitude" : "35.6403478",
    "user_id" : 1,
    "created_at" : "2015-12-26 07:10:25",
    "updated_at" : "2015-12-26 07:10:25",
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
            'company_id' => $request->input('company_id'),
        );
        $rules = array(
            'company_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $company = Company::where('id', $request->company_id)
                ->get()
                ->map(function ($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name,
                        'email' =>$company->email,
                        'address' => $company->address,
                        'website' => $company->website,
                        'phone' => $company->phone,
                        'mobile' => $company->mobile,
                        'fax' => $company->fax,
                        'title' => $company->title,
                        'company_avatar' => $company->company_avatar,
                        'main_contact_person' => $company->contactPerson->full_name,
                        'sales_team' => $company->salesTeam->salesteam,
                        'country' => $company->country_id,
                        'state' => $company->state_id,
                        'city' => $company->city_id,
                    ];
                });
            
            return response()->json(['company' => $company->toArray()], 200);
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
     * Edit company
     *
     * @Post("/edit_company")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "company_id":"1","name":"Company name","email":"email@email.com"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'company_id' => $request->input('company_id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        );
        $rules = array(
            'company_id' => 'required',
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $company = Company::find($request->company_id);
            $company->update($request->except('token','company_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete company
     *
     * @Post("/delete_company")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "company_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $data = array(
            'company_id' => $request->input('company_id'),
        );
        $rules = array(
            'company_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $company = Company::find($request->company_id);
            $company->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
