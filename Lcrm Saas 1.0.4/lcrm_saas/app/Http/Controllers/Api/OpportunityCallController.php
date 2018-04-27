<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Opportunity;
use JWTAuth;
use Validator;
use Illuminate\Http\Request;

use App\Http\Requests;

class OpportunityCallController extends Controller
{
    private $user;

    /**
     * Get all opportunity calls
     *
     * @Get("/opportunity_calls")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity_id":"1"}),
     *      @Response(200, body={
    "calls": {
    {
    "id": 1,
    "date": "2015-10-15",
    "call_summary": "Call summary",
    "company": "Company",
    "responsible": "User",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function index(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $opportunity = Opportunity::find($request->opportunity_id);
        $calls = $opportunity->calls()
            ->with('responsible', 'company')
            ->get()
            ->map(function ($call) {
                return [
                    'id' => $call->id,
                    'date' => $call->date,
                    'call_summary' => $call->call_summary,
                    'company' => $call->company->name,
                    'responsible' => $call->responsible->full_name
                ];
            })->toArray();

        return response()->json(['calls' => $calls], 200);
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
     * Post opportunity call
     *
     * @Post("/post_opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity_id' => $request->input('opportunity_id'),
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'date' => 'required|date',
            'opportunity_id' => "required",
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = Opportunity::find($request->opportunity_id);
            $call = $opportunity->calls()->create($request->except('token','opportunity_id'), ['user_id' => $this->user->id]);
            $call->user_id = $this->user->id;
            $call->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get opportunity call
     *
     * @Get("/opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "opportunity_id":"1","call_id":"1"}),
     *       @Response(200, body={"call": {
    "id": 1,
    "date": "2015-10-15",
    "call_summary": "Call summary",
    "company": "Company",
    "responsible": "User",
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */

    public function show(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $opportunity = Opportunity::find($request->opportunity_id);
        $call = $opportunity->calls()->where('call_id',$request->call_id)
            ->with('responsible', 'company')
            ->get()
            ->map(function ($call) {
                return [
                    'id' => $call->id,
                    'date' => $call->date,
                    'call_summary' => $call->call_summary,
                    'company' => $call->company->name,
                    'responsible' => $call->responsible->full_name
                ];
            })->toArray();

        return response()->json(['call' => $call], 200);
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
     * Edit opportunity call
     *
     * @Post("/edit_opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "opportunity_id":"1","lead_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'call_id' => $request->input('call_id'),
            'opportunity_id' => $request->input('opportunity_id'),
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'call_id' => 'required',
            'opportunity_id' => "required",
            'date' => 'required|date',
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $call = Call::find($request->call_id);
            $call->update($request->except('token','call_id','opportunity_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete opportunity call
     *
     * @Post("/delete_opportunity_call")
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
            'call_id' => $request->input('call_id'),
        );
        $rules = array(
            'call_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $call = Call::find($request->call_id);
            $call->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
