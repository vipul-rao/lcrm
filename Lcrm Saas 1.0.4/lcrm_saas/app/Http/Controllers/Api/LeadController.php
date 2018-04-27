<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Lead;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;

use App\Http\Requests;

class LeadController extends Controller
{
    private $user;

    /**
     * Get all leads
     *
     * @Get("/leads")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "leads": {
    {
    "id": 1,
    "register_time": "2015-12-22",
    "opportunity": "1.2",
    "contact_name": "Contact name",
    "email": "dsad@asd.com",
    "phone": "456469465",
    "salesteam": "Test team",
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
        $leads = Lead::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('country', 'salesteam')
            ->get()
            ->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'register_time' => $lead->register_time,
                    'opportunity' => $lead->opportunity,
                    'contact_name' => $lead->contact_name,
                    'country' => $lead->country->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'salesteam' => $lead->salesTeam->salesteam
                ];
            })->toArray();

        return response()->json(['leads' => $leads], 200);
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
     * Post lead
     *
     * @Post("/post_lead")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity":"125.5", "email":"test@test.com","customer_id":"12","sales_team_id":"1","tags":"Softwae","country_id":"15"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer_id' => $request->input('customer_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'sales_person_id' => $request->input('sales_person_id'),
            'country_id' => $request->input('country_id'),
            'phone' => $request->input('phone'),
        );
        $rules = array(
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer_id' => 'required',
            'sales_team_id' => 'required',
            'sales_person_id' => "required",
            'country_id' => "required",
            'phone' => "required"
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

//            $this->user->leads(new Lead($request->except('token')));
            $this->user->leads()->create($request->except('token'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get lead item
     *
     * @Get("/lead")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "lead_id":"1"}),
     *       @Response(200, body={"invoice": {
    "id" : 1,
    "opportunity" : "Lead",
    "company_name" : "sdfsdf sdf",
    "customer_id" : 1,
    "address" : "sd fsdfd",
    "country_id" : 1,
    "state_id" : 43,
    "city_id" : 5914,
    "sales_person_id" : 1,
    "sales_team_id" : 1,
    "contact_name" : "sdfsdf sdf sdf ",
    "title" : "Doctor",
    "email" : "user@crm.com",
    "function" : "asdasd sad asd ",
    "phone" : "1545",
    "mobile" : "545",
    "fax" : "1545",
    "tags" : "2,4",
    "priority" : "Low",
    "internal_notes" : "asd asd asd ",
    "assigned_partner_id" : 0,
    "user_id" : 1,
    "created_at" : "2015-12-22 19:56:54",
    "updated_at" : "2015-12-22 19:56:54",
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
            'lead_id' => $request->input('lead_id'),
        );
        $rules = array(
            'lead_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $lead=Lead::where('id', $request->lead_id)
                ->get()
                ->map(function ($lead) {
                    return [
                        'id' => $lead->id,
                        'opportunity' => $lead->opportunity,
                        'company' => $lead->company_name,
                        'customer' => Company::find($lead->customer_id)->name,
                        'address' => $lead->address,
                        'country' => Country::find($lead->country_id)->name,
                        'state_id' => State::find($lead->state_id)->name,
                        'city_id' => City::find($lead->city_id)->name,
                        'salesteam' => Salesteam::find($lead->sales_team_id)->salesteam,
                        'sales_person' => User::find($lead->sales_person_id)->full_name,
                        'contact_name' => $lead->contact_name,
                        'title' => $lead->title,
                        'email' => $lead->email,
                        'function' => $lead->function,
                        'phone' => $lead->phone,
                        'mobile' => $lead->mobile,
                        'fax' => $lead->fax,
                        'tags' => $lead->tags,
                        'priority' => $lead->priority,
                        'internal_notes' => $lead->internal_notes];
                });
            return response()->json(['lead' => $lead], 200);
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
     * Edit lead
     *
     * @Post("/edit_lead")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo","lead_id":1, "opportunity":"125.5", "email":"test@test.com","customer_id":"12","sales_team_id":"1","tags":"Softwae","country_id":"15"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'lead_id' => $request->input('lead_id'),
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer_id' => $request->input('customer_id'),
            'country_id' => $request->input('country_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'sales_person_id' => $request->input('sales_person_id'),
        );
        $rules = array(
            'lead_id' => 'required',
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer_id' => 'required',
            'country_id' => 'required',
            'sales_team_id' => 'required',
            'sales_person_id' => "required"
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $lead = Lead::find($request->lead_id);
            $lead->tags = implode(',', $request->get('tags', []));
            $lead->update($request->except('token','tags', 'lead_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete lead
     *
     * @Post("/delete_lead")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "lead_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'lead_id' => $request->input('lead_id'),
        );
        $rules = array(
            'lead_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $lead = Lead::find($request->lead_id);
            $lead->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
