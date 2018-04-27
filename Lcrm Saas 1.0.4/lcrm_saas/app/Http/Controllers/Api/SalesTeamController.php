<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Salesteam;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class SalesTeamController extends Controller
{
    private $user;
    /**
     * Get all salesteams
     *
     * @Get("/salesteams")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "salesteam": {
    {
    "id": 1,
    "salesteam": "Name of team",
    "target": "111",
    "invoice_forecast": "1125",
    "actual_invoice": "205",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     *     })
     */
    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $salesteam = Salesteam::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->latest()->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'salesteam' => $user->salesteam,
                'target' => $user->invoice_target,
                'invoice_forecast' => $user->invoice_forecast,
                'actual_invoice' => $user->actual_invoice,
            ];
        })->toArray();

        return response()->json(['salesteams' => $salesteam], 200);
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
     * Post salesteam
     *
     * @Post("/post_salesteam")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","salesteam":"Title", "invoice_target":"8","invoice_forecast":"1","team_leader":"12","team_members":"1,2,5"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'salesteam' => $request->input('salesteam'),
            'invoice_target' => $request->input('invoice_target'),
            'invoice_forecast' => $request->input('invoice_forecast'),
            'team_leader' => $request->input('team_leader'),
            'team_members' => $request->input('team_members'),
        );
        $rules = array(
            'salesteam' => 'required',
            'invoice_target' => 'required',
            'invoice_forecast' => 'required',
            'team_leader' => 'required',
            'team_members' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $salesteam = new Salesteam($request->except('token'));
            $salesteam->team_members = implode(',', $request->get('team_members', []));
           // $salesteam->register_time = strtotime(date('d F Y g:i a'));
//            $salesteam->ip_address = $request->server('REMOTE_ADDR');
            $salesteam->quotations = ($request->quotations) ? $request->quotations : 0;
            $salesteam->leads = ($request->leads) ? $request->leads : 0;
            $salesteam->opportunities = ($request->opportunities) ? $request->opportunities : 0;
//            $salesteam->status = ($request->status) ? $request->status : 0;
            $this->user->salesTeams()->save($salesteam);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get salesteam item
     *
     * @Get("/salesteam")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "salesteam_id":"1"}),
     *       @Response(200, body={"salesteam": {
    "id" : 1,
    "salesteam" : "testera tim 1",
    "team_leader" : 2,
    "quotations" : false,
    "leads" : false,
    "opportunities" : false,
    "invoice_target" : 15,
    "invoice_forecast" : 22,
    "actual_invoice" : 0,
    "notes" : "dfg fdg dfg",
    "user_id" : 1,
    "created_at" : "2015-12-22 19:47:18",
    "updated_at" : "2015-12-22 19:47:29",
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
            'salesteam_id' => $request->input('salesteam_id'),
        );
        $rules = array(
            'salesteam_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $salesteam = Salesteam::where('id', $request->salesteam_id)
                ->get()
                ->map(function ($salesteam) {
                    return [
                        'id' => $salesteam->id,
                        'salesteam' => $salesteam->salesteam,
                        'team_leader' => $salesteam->teamLeader->full_name,
                        'quotations' => $salesteam->quotations,
                        'leads' => $salesteam->leads,
                        'opportunities' => $salesteam->opportunities,
                        'invoice_target' => $salesteam->invoice_target,
                        'invoice_forecast' => $salesteam->invoice_forecast,
                        'actual_invoice' => $salesteam->actual_invoice,
                        'notes' => $salesteam->notes
                    ];
                });
            return response()->json(['salesteam' => $salesteam], 200);
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
     * Edit salesteam
     *
     * @Post("/edit_salesteam")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "salesteam_id":"1","salesteam":"Title", "invoice_target":"8","invoice_forecast":"1","team_leader":"12","team_members":"1,2,5"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'salesteam_id' => $request->input('salesteam_id'),
            'salesteam' => $request->input('salesteam'),
            'invoice_target' => $request->input('invoice_target'),
            'invoice_forecast' => $request->input('invoice_forecast'),
            'team_leader' => $request->input('team_leader'),
            'team_members' => $request->input('team_members'),
        );
        $rules = array(
            'salesteam_id' => 'required',
            'salesteam' => 'required',
            'invoice_target' => 'required',
            'invoice_forecast' => 'required',
            'team_leader' => 'required',
            'team_members' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $salesteam = Salesteam::find($request->salesteam_id);
            $salesteam->team_members = implode(',', $request->get('team_members', []));
            $salesteam->quotations = ($request->quotations) ? $request->quotations : 0;
            $salesteam->leads = ($request->leads) ? $request->leads : 0;
            $salesteam->opportunities = ($request->opportunities) ? $request->opportunities : 0;
//            $salesteam->status = ($request->status) ? $request->status : 0;
            $salesteam->update($request->except('token','team_members', 'quotations', 'leads', 'opportunities', 'status', 'salesteam_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete salesteam
     *
     * @Post("/delete_salesteam")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "salesteam_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'salesteam_id' => $request->input('salesteam_id'),
        );
        $rules = array(
            'salesteam_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $salesteam = Salesteam::find($request->salesteam_id);
            $salesteam->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
