<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Salesteam;
use App\Models\User;
use App\Repositories\UserSettingRepository;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class OpportunityController extends Controller
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
     * Get all opportunities
     *
     * @Get("/opportunities")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "opportunities": {
    {
    "id": 1,
    "opportunity": "Opportunity",
    "company": "Company",
    "next_action": "2015-12-22",
    "stages": "Stages",
    "expected_revenue": "Expected revenue",
    "probability": "probability",
    "salesteam": "salesteam",
    "calls": "5",
    "meetings": "5"
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
        $opportunities = Opportunity::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('salesteam', 'customer', 'calls', 'meetings')
            ->get()
            ->map(function ($opportunity) {
                return [
                    'id' => $opportunity->id,
                    'opportunity' => $opportunity->opportunity,
                    'company' => isset($opportunity->customer) ? $opportunity->customer->name : '',
                    'next_action' => $opportunity->next_action,
                    'stages' => $opportunity->stages,
                    'expected_revenue' => $opportunity->expected_revenue,
                    'probability' => $opportunity->probability,
                    'salesteam' => isset($opportunity->salesteam) ? $opportunity->salesteam->salesteam : '',
                    'calls' => $opportunity->calls->count(),
                    'meetings' => $opportunity->meetings->count()
                ];
            })->toArray();

        return response()->json(['opportunities' => $opportunities], 200);
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
     * Post opportunity
     *
     * @Post("/post_opportunity")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity":"Opportunity", "email":"email@email.com","customer":"1","sales_team_id":"1","next_action":"2015-11-11","expected_closing":"2015-11-11"}),
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
            'next_action' => $request->input('next_action'),
            'expected_closing' => $request->input('expected_closing'),
        );
        $rules = array(
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer_id' => 'required',
            'sales_team_id' => 'required',
            'next_action' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
            'expected_closing' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = new Opportunity($request->except('token'));
            if (isset($request->tags)) {
                $opportunity->tags = implode(',', $request->tags);
            }
//            $opportunity->register_time = strtotime(date('d F Y g:i a'));
//            $opportunity->ip_address = $request->ip();

            $this->user->opportunities()->save($opportunity);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get opportunity item
     *
     * @Get("/opportunity")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "opportunity_id":"1"}),
     *       @Response(200, body={"opportunity": {
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
            'opportunity_id' => $request->input('opportunity_id'),
        );
        $rules = array(
            'opportunity_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $opportunity = Opportunity::where('id', $request->opportunity_id)
                ->get()
                ->map(function ($opportunity) {
                    return [
                        'id' => $opportunity->id,
                        'opportunity' => $opportunity->opportunity,
                        'company' => Company::find($opportunity->customer_id)->name,
                        'next_action' => $opportunity->next_action,
                        'next_action_title' => $opportunity->next_action_title,
                        'email' => $opportunity->email,
                        'phone' => $opportunity->phone,
                        'priority' => $opportunity->priority,
                        'stages' => $opportunity->stages,
                        'expected_revenue' => $opportunity->expected_revenue,
                        'probability' => $opportunity->probability,
                        'salesteam' => Salesteam::find($opportunity->sales_team_id)->salesteam,
                        'calls' => $opportunity->calls->count(),
                        'meetings' => $opportunity->meetings->count(),
                        'sales_person' => User::find($opportunity->sales_person_id)->full_name
                    ];
                })->toArray();
            return response()->json(['opportunity' => $opportunity->toArray()], 200);
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
     * Edit opportunity
     *
     * @Post("/edit_opportunity")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo","opportunity_id":1,"opportunity":"Opportunity", "email":"email@email.com","customer":"1","sales_team_id":"1","next_action":"2015-11-11","expected_closing":"2015-11-11"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity_id' => $request->input('opportunity_id'),
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer_id' => $request->input('customer_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'next_action' => $request->input('next_action'),
            'expected_closing' => $request->input('expected_closing'),
        );
        $rules = array(
            'opportunity_id' => 'required',
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer_id' => 'required',
            'sales_team_id' => 'required',
            'next_action' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
            'expected_closing' => 'required|date_format:"'. $this->userSettingRepository->getValue('date_format').'"',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = Opportunity::find($request->opportunity_id);
            if (isset($request->tags)) {
                $opportunity->tags = implode(',', $request->tags);
            }
            $opportunity->update($request->except('token','tags', 'opportunity_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete opportunity
     *
     * @Post("/delete_opportunity")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $data = array(
            'opportunity_id' => $request->input('opportunity_id'),
        );
        $rules = array(
            'opportunity_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $opportunity = Opportunity::find($request->opportunity_id);
            $opportunity->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
