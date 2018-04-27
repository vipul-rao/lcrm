<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use JWTAuth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    /**
     * Get all tasks
     *
     * @Get("/tasks")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "tasks": {
    {
    "id": 1,
    "task_from": "username",
    "finished": "0",
    "task_deadline": "2015-11-11",
    "task_description": "asasd"
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
        $tasks= Task::where('user_id', $this->user->id)
            ->orderBy("finished", "ASC")
            ->orderBy("task_deadline", "DESC")
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'task_from' => $task->task_from_users->full_name,
                    'finished' => $task->finished,
                    'task_deadline' => $task->task_deadline,
                    "task_description" => $task->task_description,
                ];
            })->toArray();


        return response()->json(['tasks' => $tasks], 200);
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
     * Post task
     *
     * @Post("/post_task")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo","user_id":"1", "task_description":"asasas","task_deadline":"2016-10-10"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     **/

    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'user_id' => $request->input('user_id'),
            'task_description' => $request->input('task_description'),
            'task_deadline' => $request->input('task_deadline')
        );
        $rules = array(
            'user_id' => 'required|integer',
            'task_description' => 'required',
            'task_deadline' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $request->merge(['task_from_user'=>$this->user->id]);
            $task = new Task($request->except('token', 'full_name'));
            $task->save();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Edit task
     *
     * @Post("/edit_task")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "task_id":"1","user_id":"1", "task_description":"asasas","task_deadline":"2016-10-10"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */

    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'task_id' => $request->input('task_id'),
            'user_id' => $request->input('user_id'),
            'task_description' => $request->input('task_description'),
            'task_deadline' => $request->input('task_deadline')
        );
        $rules = array(
            'task_id' => 'required',
            'user_id' => 'required|integer',
            'task_description' => 'required',
            'task_deadline' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $request->merge(['task_from_user'=>$this->user->id]);
            $task = Task::find($request->task_id);
            $task->update($request->except('token','task_id'));
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete task
     *
     * @Post("/delete_task")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "task_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'task_id' => $request->input('task_id'),
        );
        $rules = array(
            'task_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $task = Task::find($request->task_id);
            $task->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
