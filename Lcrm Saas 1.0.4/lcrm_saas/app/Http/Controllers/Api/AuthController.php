<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Sentinel;

class AuthController extends Controller
{
    /**
     * Login to system
     *
     * @Post("/login")
     * @Versions({"v1"})
     * @Transaction({
     *  @Request({"email": "foo@bar.com","password": "bar"}),
     *  @Response(200, body={
            "token": "token",
            "user": {
            "id": 4,
            "first_name": "Teacher",
            "last_name": "Doe",
            "email": "teacher@sms.com",
            "phone_number": "465465415",
            "user_id": "1",
            "user_avatar": "image.jpg",
            "permissions" : "{sales_team.read:true,sales_team.write:true,sales_team.delete:true,leads.read:true,leads.write:true,leads.delete:true,opportunities.read:true,opportunities.write:true,opportunities.delete:true,logged_calls.read:true,logged_calls.write:true,logged_calls.delete:true,meetings.read:true,meetings.write:true,meetings.delete:true,products.read:true,products.write:true,products.delete:true,quotations.read:true,quotations.write:true,quotations.delete:true,sales_orders.read:true,sales_orders.write:true,sales_orders.delete:true,invoices.read:true,invoices.write:true,invoices.delete:true,pricelists.read:true,pricelists.write:true,pricelists.delete:true,contracts.read:true,contracts.write:true,contracts.delete:true,staff.read:true,staff.write:true,staff.delete:true}",
            "ends_at": "2015-12-07 09:49:19",
            },
            "role": "user"
     *   }),
     *   @Response(401, body={
    "error": "invalid_credentials"
     *   }),
     *   @Response(500, body={
    "error": "could_not_create_token"
     *   })
    })
     */
    public function login(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        // all good so return the data
        Sentinel::authenticate($request->only('email', 'password'), $request['remember-me']);
        $user = Sentinel::getUser();
        if ($user->inRole('admin')) {
            $role = 'admin';
        }
        elseif ($user->inRole('user')) {
            $role = 'user';
        }
        elseif ($user->inRole('staff')) {
            $role = 'staff';
        }
        elseif ($user->inRole('customer')) {
            $role = 'customer';
        }
        else{
            $role = 'no_role';
        }
        $user = User::select('id','first_name','last_name', 'email', 'phone_number','user_id','user_avatar')->find(Sentinel::getUser()->id);

        return response()->json(['token'=> $token,
            'user' => $user,
            'role' => $role], 200);
    }
}
