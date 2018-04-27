<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use Sentinel;
use App\Http\Requests;
use App\Repositories\UserRepository;

class UserCustomerController extends Controller
{
    private $user;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->middleware('authorized:contacts.read', ['only' => ['index', 'data']]);
        $this->middleware('authorized:contacts.write', ['only' => ['create', 'store', 'update', 'edit']]);
        $this->middleware('authorized:contacts.delete', ['only' => ['delete']]);

        $this->userRepository = $userRepository;

    }

    /**
     * Get all customers
     *
     * @Get("/customers")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "customers": {
    {
    "id": 1,
    "full_namae": "full namae",
    "email": "email@email.com",
    "created_at": "2015--11-11",
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
        
        $customers = $this->userRepository->getAll()
            ->with('customer')
            ->get()
            ->filter(function ($user) {
                return $user->inRole('customer');
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-d-m')
                ];
            })->toArray();

        return response()->json(['customers' => $customers], 200);
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
     * Post customer
     *
     * @Post("/post_customer")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "first_name":"first name", "last_name":"last name","email":"email@email.com","password":"password"}),
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
            'password' => 'required|min:6|confirmed',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $user = Sentinel::registerAndActivate($request->only('first_name', 'last_name', 'phone_number', 'email', 'password'));

            $customer = new Customer($request->except('first_name', 'last_name', 'phone_number', 'email', 'password', 'user_avatar', 'password_confirmation'));
            $customer->user_id = $user->id;
            $customer->belong_user_id = $this->user->id;
            $customer->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Get customer item
     *
     * @Get("/customer")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "customer_id":"1"}),
     *       @Response(200, body={"contract": {
    "id" : 1,
    "user_id" : 3,
    "belong_user_id" : 2,
    "address" : "",
    "website" : "",
    "job_position" : "",
    "mobile" : "5456",
    "fax" : "",
    "title" : "",
    "company_avatar" : "",
    "company_id" : 0,
    "sales_team_id" : 0,
    "created_at" : "2015-12-22 19:26:19",
    "updated_at" : "2015-12-28 19:07:58",
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
            'customer_id' => $request->input('customer_id'),
        );
        $rules = array(
            'customer_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $customer = Customer::where('id', $request->customer_id)
                ->get()
                ->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'full_name' => $customer->user->full_name,
                        'email' => $customer->user->email,
                        'website' => $customer->website,
                        'mobile' => $customer->mobile,
                        'company' => $customer->company->name,
                        'salesteam' => $customer->salesTeam->salesteam,
                        'address' => $customer->address,
                        'title' => $customer->title,
                        'created_at' => $customer->created_at->format('Y-d-m')
                    ];
                });
            return response()->json(['customer' => $customer], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param   \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
    }

    /**
     * Edit customer
     *
     * @Post("/edit_customer")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "customer_id":"1","first_name":"first name", "last_name":"last name","email":"email@email.com","password":"password"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function update(Request $request, $id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'customer_id' => $request->input('customer_id'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        );
        $rules = array(
            'customer_id' => 'required',
            'first_name' => 'required|min:3|max:50',
            'last_name' => 'required|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $customer = Customer::find($request->customer_id);
            $customer->update($request->except('customer_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete customer
     *
     * @Post("/delete_customer")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "customer_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function destroy(Request $request)
    {
        $data = array(
            'customer_id' => $request->input('customer_id'),
        );
        $rules = array(
            'customer_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $customer = Customer::find($request->customer_id);
            $customer->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }
}
