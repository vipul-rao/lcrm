<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class LeadCallController extends Controller
{
    private $user;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Post lead call
     *
     * @Post("/post_lead_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "lead_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'lead_id' => $request->input('lead_id'),
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'date' => 'required|date',
            'lead_id' => "required",
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $lead = Lead::find($request->lead_id);
            $call = $lead->calls()->create($request->except('token'), ['user_id' => $this->user->id]);
            $call->user_id = $this->user->id;
            $call->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get all lead call
     *
     * @Get("/lead_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "lead_id":"1"}),
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
    public function show(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $lead = Lead::find($request->lead_id);
        $calls = $lead->calls()
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
     * Edit lead call
     *
     * @Post("/edit_lead_call")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "call_id":"1","lead_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
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
            'lead_id' => $request->input('lead_id'),
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'call_id' => 'required',
            'lead_id' => "required",
            'date' => 'required|date',
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $call = Call::find($request->call_id);
            $call->update($request->except('token','call_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete lead call
     *
     * @Post("/delete_lead_call")
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
