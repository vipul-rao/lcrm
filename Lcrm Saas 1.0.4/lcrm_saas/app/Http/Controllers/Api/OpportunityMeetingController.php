<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use App\Http\Requests;

class OpportunityMeetingController extends Controller
{
    private $user;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $opportunity = Opportunity::find($request->opportunity_id);
        $meetings = $opportunity->meetings()
            ->with('responsible')
            ->get()
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'meeting_subject' => $meeting->meeting_subject,
                    'starting_date' => $meeting->starting_date,
                    'ending_date' => $meeting->ending_date,
                    'responsible' => isset($meeting->responsible) ? $meeting->responsible->full_name : 'N/A'
                ];
            })->toArray();

        return response()->json(['meetings' => $meetings], 200);
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
     * Post opportunity meeting
     *
     * @Post("/post_opportunity_meeting")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity_id":1, "meeting_subject":"Subject", "starting_date":"2015-11-11","ending_date":"2015-11-11"}),
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
            'meeting_subject' => $request->input('meeting_subject'),
            'starting_date' => $request->input('starting_date'),
            'ending_date' => $request->input('ending_date'),
        );
        $rules = array(
            'opportunity_id' => 'required',
            'meeting_subject' => 'required',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = Opportunity::find($request->opportunity_id);
            $request->merge([
                'attendees' => implode(',', $request->get('attendees', []))
            ]);
            $opportunity->meetings()->create($request->except('token','opportunity_id'), ['user_id' => $this->user->id]);
            $opportunity->user_id = $this->user->id;
            $opportunity->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get all opportunity meeting
     *
     * @Get("/opportunity_meeting")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","opportunity_id":"1"}),
     *      @Response(200, body={
    "salesteam": {
    {
    "id": 1,
    "meeting_subject": "meeting subject",
    "starting_date": "2015-12-22",
    "ending_date": "2015-12-22",
    "responsible": "User name"
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
        $opportunity = Opportunity::find($request->opportunity_id);
        $meeting = $opportunity->meetings()->where('meeting_id',$request->meeting_id)
            ->with('responsible')
            ->get()
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'meeting_subject' => $meeting->meeting_subject,
                    'starting_date' => $meeting->starting_date,
                    'ending_date' => $meeting->ending_date,
                    'responsible' => isset($meeting->responsible) ? $meeting->responsible->full_name : 'N/A'
                ];
            })->toArray();

        return response()->json(['meeting' => $meeting], 200);
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
     * Edit opportunity meeting
     *
     * @Post("/edit_opportunity_meeting")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo","meeting_id":1, "opportunity_id":1, "meeting_subject":"Subject", "starting_date":"2015-11-11","ending_date":"2015-11-11"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'meeting_id' => $request->input('meeting_id'),
            'opportunity_id' => $request->input('meeting_id'),
            'meeting_subject' => $request->input('meeting_subject'),
            'starting_date' => $request->input('starting_date'),
            'ending_date' => $request->input('ending_date'),
        );
        $rules = array(
            'meeting_id' => 'required',
            'opportunity_id' => 'required',
            'meeting_subject' => 'required',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $meeting = Meeting::find($request->meeting_id);
            $meeting->attendees = implode(',', $request->get('attendees', []));
            $meeting->update($request->except('token','attendees','opportunity_id','meeting_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete opportunity meeting
     *
     * @Post("/delete_opportunity_meeting")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "meeting_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'meeting_id' => $request->input('meeting_id'),
        );
        $rules = array(
            'meeting_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $meeting = Meeting::find($request->meeting_id);
            $meeting->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
