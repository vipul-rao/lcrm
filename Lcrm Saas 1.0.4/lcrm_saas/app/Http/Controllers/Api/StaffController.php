<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use Sentinel;
use App\Http\Requests;

class StaffController extends Controller
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
     * Post staff
     *
     * @Post("/post_staff")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","first_name":"first name", "last_name":"last name","email":"email@email.com","password":"1password"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        );
        $rules = array(
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $user = Sentinel::registerAndActivate($request->only('first_name', 'last_name', 'email', 'password'));

            $role = Sentinel::findRoleBySlug('staff');
            $role->users()->attach($user);

            $permissions=explode(",", $request->get('permissions'));
            foreach ($permissions as $permission) {
                $user->addPermission($permission);
            }

            $user->user_id = Sentinel::getUser()->user_id;
            $user->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get all staff
     *
     * @Get("/staff")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "staff": {
    {
    "id": 1,
    "full_name": "product name",
    "email": "email@email.com",
    "created_at": "2015-11-11"
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
        $staff = User::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format(Settings::get('date_format')),
                ];
            })->toArray();

        return response()->json(['staff' => $staff], 200);
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
     * Edit staff
     *
     * @Post("/edit_staff")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "staff_id":"1","first_name":"first name", "last_name":"last name","password":"1password"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'staff_id' => $request->input('staff_id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'password' => $request->input('password'),
        );
        $rules = array(
            'staff_id' => 'required',
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'required|min:3|max:50',
            'password' => 'required|min:6',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $staff = User::find($request->staff_id);

            foreach ($staff->getPermissions() as $key => $item) {
                $staff->removePermission($key);
            }

            $permissions=explode(",", $request->get('permissions'));
            foreach ($permissions as $permission) {
                $staff->addPermission($permission);
            }

            $staff->update($request->except('token','staff_id', 'email'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete staff
     *
     * @Post("/delete_staff")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "staff_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'staff_id' => $request->input('staff_id'),
        );
        $rules = array(
            'staff_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $staff = User::find($request->staff_id);
            $staff->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
