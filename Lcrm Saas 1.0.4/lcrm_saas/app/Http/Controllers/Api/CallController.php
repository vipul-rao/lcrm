<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Repositories\UserSettingRepository;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class CallController extends Controller
{
    private $user;
    private $userSettingRepository;
    public function __construct(
        UserSettingRepository $userSettingRepository)
    {

        parent::__construct();
        $this->userSettingRepository = $userSettingRepository;
    }

    /**
     * Get all calls
     *
     * @Get("/calls")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "calls": {
    {
    "id": 1,
    "date": "2015-10-15",
    "call_summary": "Call summary",
    "company": "Company",
    "user": "User",
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
        $calls = Call::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('user', 'company')
            ->get()
            ->map(function ($call) {
                return [
                    'id' => $call->id,
                    'date' => $call->date,
                    'call_summary' => $call->call_summary,
                    'company' => $call->company->name,
                    'user' => $call->user->full_name,
                ];
            })->toArray();

        return response()->json(['calls' => $calls], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Post call
     *
     * @Post("/post_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {

        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'date' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->calls()->create($request->except('token'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get call item
     *
     * @Get("/call")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "call_id":"1"}),
     *       @Response(200, body={"call": {
    "id" : 1,
    "opportunity" : "r dfgfdg dfg",
    "stages" : "New",
    "customer_id" : 1,
    "expected_revenue" : "sad asd ",
    "probability" : "0",
    "email" : "admin@gmail.com",
    "phone" : 787889,
    "sales_person_id" : 2,
    "sales_team_id" : 1,
    "next_action" : "21.12.2015.",
    "next_action_title" : "454545",
    "expected_closing" : "29.12.2015.",
    "priority" : "Low",
    "tags" : "1,3",
    "lost_reason" : "Too expensive",
    "internal_notes" : "ghkhjkhjk",
    "assigned_partner_id" : 1,
    "user_id" : 1,
    "created_at" : "2015-12-22 20:17:20",
    "updated_at" : "2015-12-22 20:19:11",
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
            'call_id' => $request->input('call_id'),
        );
        $rules = array(
            'call_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $call = Call::where('id', $request->call_id)
                ->get()
                ->map(function ($call) {
                    return [
                        'id' => $call->id,
                        'date' => $call->date,
                        'call_summary' => $call->call_summary,
                        'duration' => $call->duration,
                        'company' => $call->company->name,
                        'resp_staff' => $call->responsible->full_name,
                    ];
                });
            return response()->json(['call' => $call], 200);
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
     * Edit call
     *
     * @Post("/edit_call")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "call_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
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
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'call_id' => 'required',
            'date' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
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
     * Delete call
     *
     * @Post("/delete_call")
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
