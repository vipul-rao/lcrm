<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use JWTAuth;
use Validator;
use Sentinel;
use Illuminate\Http\Request;

use App\Http\Requests;

class MeetingController extends Controller
{
    private $user;

    /**
     * Get all meetings
     *
     * @Get("/meetings")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "meetings": {
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
    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $meetings = Meeting::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('responsible')
            ->latest()->get()->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'meeting_subject' => $meeting->meeting_subject,
                    'starting_date' => $meeting->starting_date,
                    'ending_date' => $meeting->ending_date,
                    'responsible' => $meeting->responsible->full_name
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
     * Post meeting
     *
     * @Post("/post_meeting")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "meeting_subject":"Subject", "starting_date":"2015-11-11","ending_date":"2015-11-11"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'meeting_subject' => $request->input('meeting_subject'),
            'starting_date' => $request->input('starting_date'),
            'ending_date' => $request->input('ending_date'),
            'responsible_id' => $request->input('responsible_id'),
        );
        $rules = array(
            'meeting_subject' => 'required',
            'responsible_id' => 'required',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $request->merge([
                'attendees' => implode(',', $request->get('attendees', []))
            ]);

            $user = Sentinel::getUser();
            $user->meetings()->create($request->except('token'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get meeting item
     *
     * @Get("/meeting")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "meeting_id":"1"}),
     *       @Response(200, body={"meeting": {
    "id" : 2,
    "meeting_subject" : "Meeting",
    "attendees" : "1",
    "responsible_id" : 2,
    "starting_date" : "29.12.2015. 00:00",
    "ending_date" : "08.01.2016. 00:00",
    "all_day" : 0,
    "location" : "sdfsdf",
    "meeting_description" : "ftyf hgfhgfh",
    "privacy" : "Everyone",
    "show_time_as" : "Free",
    "duration" : "",
    "user_id" : 0,
    "created_at" : "2015-12-22 20:19:42",
    "updated_at" : "2015-12-26 15:03:37",
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
            'meeting_id' => $request->input('meeting_id'),
        );
        $rules = array(
            'meeting_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $meeting= Meeting::where('id', $request->meeting_id)
                ->get()
                ->map(function ($meeting) {
                    return [
                        'id' => $meeting->id,
                        'meeting_subject' => $meeting->meeting_subject,
                        'responsible' => $meeting->responsible->full_name,
                        'starting_date' => $meeting->starting_date,
                        'ending_date' => $meeting->ending_date,
                        'all_day' => $meeting->all_day,
                        'location' => $meeting->location,
                        'meeting_description' => $meeting->meeting_description,
                        'privacy' => $meeting->privacy,
                        'show_time_as' => $meeting->show_time_as,
                        'duration' => $meeting->duration
                    ];
                });
            $attendees=array();
            $meetingNew = Meeting::find($request->meeting_id);
            $attendeesArray=array();
            $attendeesArray[]=explode(",",$meetingNew->attendees);
            foreach ($attendeesArray as $key=>$attendeeId){
                do{
                    $attendees[]=Company::find($attendeeId)->pluck('name');
                }while($key==sizeof($attendeeId));
            }
            return response()->json(['meeting' => $meeting], 200);
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
     * Edit meeting
     *
     * @Post("/edit_meeting")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo","meeting_id":1, "meeting_subject":"Subject", "starting_date":"2015-11-11","ending_date":"2015-11-11"}),
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
            'meeting_subject' => $request->input('meeting_subject'),
            'responsible_id' => $request->input('responsible_id'),
            'starting_date' => $request->input('starting_date'),
            'ending_date' => $request->input('ending_date'),
        );
        $rules = array(
            'meeting_id' => 'required',
            'meeting_subject' => 'required',
            'responsible_id' => 'required',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $meeting = Meeting::find($request->meeting_id);
            $meeting->attendees = implode(',', $request->get('attendees', []));
            $meeting->update($request->except('token','attendees', 'meeting_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete meeting
     *
     * @Post("/delete_meeting")
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
