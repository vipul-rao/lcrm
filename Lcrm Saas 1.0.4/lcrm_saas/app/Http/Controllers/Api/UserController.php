<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Category;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\InvoiceReceivePayment;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\Qtemplate;
use App\Models\Quotation;
use App\Models\QuotationProduct;
use App\Models\Saleorder;
use App\Models\SaleorderProduct;
use App\Models\Salesteam;
use App\Models\Staff;
use App\Models\UserSetting;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;
use DB;

/**
 * User and staff endpoints, can be accessed only with role "user" or "staff"
 *
 * @Resource("User", uri="/user")
 */
class UserController extends Controller
{
    use Helpers;

    private $user;

    private $events = [];

    /**
     * Get all calendar items
     *
     * @Get("/calendar")
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
     * })
     */
    public function calendar()
    {
        $date = strtotime(date('Y-m-d'));
        $this->user = JWTAuth::parseToken()->authenticate();

        $quotations = Quotation::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->where('exp_date', '>', $date)
            ->with('user', 'customer')
            ->get()
            ->map(function ($quotation) {
                return [
                    'id' => $quotation->id,
                    'title' => $quotation->quotations_number,
                    'start_date' => $quotation->exp_date,
                    'end_date' => $quotation->exp_date,
                    'type' => 'quotation'
                ];
            });
        $this->add_events_to_list($quotations);

        $meetings = Meeting::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->where('starting_date', '>', $date)
            ->with('responsible')
            ->latest()->get()->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->meeting_subject,
                    'start_date' => $meeting->starting_date,
                    'end_date' => $meeting->ending_date,
                    'type' => 'meeting'
                ];
            });
        $this->add_events_to_list($meetings);

        $invoices = Invoice::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->where('due_date', '>', $date)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'title' => $invoice->invoice_number,
                    'start_date' => $invoice->invoice_date,
                    'end_date' => $invoice->invoice_date,
                    'type' => 'invoice'
                ];
            });
        $this->add_events_to_list($invoices);

        $contracts = Contract::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->where('end_date', '>', $date)
            ->with('company', 'user')
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'title' => $contract->description,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'type' => 'contract'
                ];
            });
        $this->add_events_to_list($contracts);

        $opportunities = Opportunity::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->where('next_action', '>', $date)
            ->with('salesteam', 'calls', 'meetings')
            ->get()
            ->map(function ($opportunity) {
                return [
                    'id' => $opportunity->id,
                    'title' => $opportunity->opportunity,
                    'start_date' => $opportunity->next_action,
                    'end_date' => $opportunity->expected_closing,
                    'type' => 'opportunity'
                ];
            });
        $this->add_events_to_list($opportunities);

        return response()->json(['events' => $this->events], 200);
    }

    /**
     * @param $events_data
     */
    private function add_events_to_list($events_data)
    {
        foreach ($events_data as $d) {
            $event = [];
            $start_date = date('Y-m-d', (is_numeric($d['start_date']) ? $d['start_date'] : strtotime($d['start_date'])));
            $end_date = date('Y-m-d', (is_numeric($d['end_date']) ? $d['end_date'] : strtotime($d['end_date'])));
            $event['title'] = $d['title'];
            $event['id'] = $d['id'];
            $event['start'] = $start_date;
            $event['end'] = $end_date;
            $event['allDay'] = true;
            $event['description'] = $d['title'];
            array_push($this->events, $event);
        }
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
    public function calls()
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
    public function call(Request $request)
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
            $call = Call::find($request->call_id);
            return response()->json(['call' => $call->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function postCall(Request $request)
    {
//        $this->user = JWTAuth::parseToken()->authenticate();
        if (! $this->user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }

        $data = array(
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'date' => 'required|date',
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->calls()->create($request->all());

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editCall(Request $request)
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
            'date' => 'required|date',
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $call = Call::find($request->call_id);
            $call->update($request->except('call_id'));

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
    public function deleteCall(Request $request)
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


    /**
     * Get all categories
     *
     * @Get("/categories")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "category": {
    {
    "id": 1,
    "name": "Category name",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function categories()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $categories = Category::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            })->toArray();

        return response()->json(['categories' => $categories], 200);
    }

    /**
     * Get category item
     *
     * @Get("/category")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "category_id":"1"}),
     *       @Response(200, body={"category": {
    "id" : 1,
    "name" : "Category",
    "user_id" : 1,
    "created_at" : "2015-12-23 16:58:25",
    "updated_at" : "2015-12-23 16:58:25",
    "deleted_at" : null
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function category(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'category_id' => $request->input('category_id'),
        );
        $rules = array(
            'category_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $category = Category::find($request->category_id);
            return response()->json(['category' => $category->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post category
     *
     * @Post("/post_category")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "name":"category name"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postCategory(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'category_id' => $request->input('category_id'),
        );
        $rules = array(
            'category_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->categories()->create($request->all());

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit category
     *
     * @Post("/edit_category")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "category_id":"1","name":"category name"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editCategory(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'id' => $request->input('category_id'),
            'name' => $request->input('name'),
        );
        $rules = array(
            'id' => 'required',
            'name' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $category = Category::find($request->category_id);
            $category->update($request->except('category_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete category
     *
     * @Post("/delete_category")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "call_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteCategory(Request $request)
    {
        $data = array(
            'id' => $request->input('category_id'),
        );
        $rules = array(
            'id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $category = Category::find($request->category_id);
            $category->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get all companies
     *
     * @Get("/companies")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "companies": {
    {
    "id": 1,
    "name": "Name",
    "customer": "customer name",
    "phone": "634654165456",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function companies()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $companies = Company::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->latest()->get()->map(function ($comp) {
            return [
                'id' => $comp->id,
                'name' => $comp->name,
                'customer' => $comp->user->full_name,
                'phone' => $comp->phone,
            ];
        })->toArray();

        return response()->json(['companies' => $companies], 200);
    }

    /**
     * Get company item
     *
     * @Post("/company")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "company_id":"1"}),
     *       @Response(200, body={"company": {
    "id" : 2,
    "name" : "dg dfg",
    "email" : "user@crm.com",
    "password" : "",
    "lostpw" : "",
    "address" : "fdgdfg",
    "website" : "gfdgfdg",
    "phone" : "45454",
    "mobile" : "45",
    "fax" : "4545",
    "title" : "",
    "company_avatar" : "",
    "company_attachment" : "",
    "main_contact_person" : 3,
    "sales_team_id" : 1,
    "country_id" : 1,
    "state_id" : 43,
    "city_id" : 5914,
    "longitude" : "63.30929400000002",
    "latitude" : "35.6403478",
    "user_id" : 1,
    "created_at" : "2015-12-26 07:10:25",
    "updated_at" : "2015-12-26 07:10:25",
    "deleted_at" : null
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function company(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'company_id' => $request->input('company_id'),
        );
        $rules = array(
            'company_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $company = Company::find($request->company_id);
            return response()->json(['company' => $company->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post company
     *
     * @Post("/post_company")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "name":"Company name","email":"email@email.com"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postCompany(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        );
        $rules = array(
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->calls()->create($request->all());

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit company
     *
     * @Post("/edit_company")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "company_id":"1","name":"Company name","email":"email@email.com"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editCompany(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'company_id' => $request->input('company_id'),
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        );
        $rules = array(
            'company_id' => 'required',
            'name' => 'required|min:3|max:50',
            'email' => 'required|email',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $company = Company::find($request->company_id);
            $company->update($request->except('company_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete company
     *
     * @Post("/delete_company")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "company_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteCompany(Request $request)
    {
        $data = array(
            'company_id' => $request->input('company_id'),
        );
        $rules = array(
            'company_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $company = Company::find($request->company_id);
            $company->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get all contracts
     *
     * @Get("/contracts")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "company": {
    {
    "id": 1,
    "start_date": "2015-11-12",
    "description": "Description",
    "name": "Company name",
    "user": "User name",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function contracts()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $contracts = Contract::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('company', 'user')
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'start_date' => $contract->start_date,
                    'description' => $contract->description,
                    'name' => $contract->company->name,
                    'user' => $contract->user->full_name
                ];
            })->toArray();

        return response()->json(['contracts' => $contracts], 200);
    }

    /**
     * Get contract item
     *
     * @Get("/contract")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "contract_id":"1"}),
     *       @Response(200, body={"contract": {
    "id" : 1,
    "start_date" : "21.12.2015.",
    "end_date" : "23.12.2015.",
    "description" : "ffdgfdg",
    "company_id" : 1,
    "resp_staff_id" : 2,
    "real_signed_contract" : "",
    "user_id" : 1,
    "created_at" : "2015-12-22 20:27:37",
    "updated_at" : "2015-12-22 20:27:37",
    "deleted_at" : null
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function contract(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'contract_id' => $request->input('contract_id'),
        );
        $rules = array(
            'contract_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $contract = Contract::find($request->contract_id);
            return response()->json(['contract' => $contract->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post contract
     *
     * @Post("/post_contract")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "start_date":"2015-11-11","end_date":"2015-11-11","description": "Description"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postContract(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->user->calls()->create($request->all());

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit contract
     *
     * @Post("/edit_contract")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "company_id":"1","name":"Company name","email":"email@email.com"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editContract(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'contract_id' => $request->input('contract_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'description' => $request->input('description'),
        );
        $rules = array(
            'company_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'description' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $contract = Contract::find($request->contract_id);
            $contract->update($request->except('contract_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete contract
     *
     * @Post("/delete_contract")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "company_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteContract(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'contract_id' => $request->input('contract_id'),
        );
        $rules = array(
            'contract_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $contract = Contract::find($request->contract_id);
            $contract->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function customers()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $customers = Customer::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->get()
            ->filter(function ($user) {
                return $user->inRole('customer');
            })
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'full_namae' => $user->full_name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('Y-d-m')
                ];
            })->toArray();

        return response()->json(['customers' => $customers], 200);
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
    public function customer(Request $request)
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
            $customer = Customer::find($request->customer_id);
            return response()->json(['customer' => $customer->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function postCustomer(Request $request)
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
    public function editCustomer(Request $request)
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
    public function deleteCustomer(Request $request)
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


    /**
     * Get all invoices
     *
     * @Get("/invoices")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "invoices": {
    {
    "id": 1,
    "invoice_number": "1465456",
    "invoice_date": "2015-11-11",
    "customer": "Customer Name",
    "unpaid_amount": "15.2",
    "status": "Status",
    "due_date": "2015-11-11",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function invoices()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoice::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'customer' => $invoice->customer->full_name,
                    'unpaid_amount' => $invoice->unpaid_amount,
                    'status' => $invoice->status,
                    'due_date' => $invoice->due_date,
                ];
            })->toArray();

        return response()->json(['invoices' => $invoices], 200);
    }

    /**
     * Get invoice item
     *
     * @Get("/invoice")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "invoice_id":"1"}),
     *       @Response(200, body={"invoice": {
    "id" : 1,
    "order_id" : 0,
    "customer_id" : 3,
    "sales_person_id" : "2",
    "sales_team_id" : 1,
    "invoice_number" : "I0001",
    "invoice_date" : "08.12.2015. 00:00",
    "due_date" : "24.12.2015. 00:00",
    "payment_term" : "10",
    "status" : "Open Invoice",
    "total" : 1221.0,
    "tax_amount" : 195.36,
    "grand_total" : 1416.36,
    "discount" : 10,
    "final_price" : 1216.36,
    "unpaid_amount" : 1173.06,
    "user_id" : 1,
    "created_at" : "2015-12-23 18:05:35",
    "updated_at" : "2015-12-28 19:21:48",
    "deleted_at" : null,
    },"products": {
    "product" : "product",
    "description" : "description",
    "quantity" : 3,
    "unit_price" : 1.95,
    "taxes" : 1.55,
    "subtotal" : 195.36,
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function invoice(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'invoice_id' => $request->input('invoice_id'),
        );
        $rules = array(
            'invoice_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::find($request->invoice_id);
            $products = array();
            if ($invoice->products->count() > 0) {
                $sales_tax = 0;
                $values = UserSetting::whereHas('user', function ($q) {
                    $q->where(function ($query) {
                        $query
                            ->orWhere('id', $this->user->parent->id)
                            ->orWhere('users.user_id', $this->user->parent->id);
                    });
                })->where('key','sales_tax');
                if ($values->count()==1) {
                    $sales_tax = $values->first()->value;
                }
                foreach ($invoice->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * $sales_tax / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['invoice' => $invoice->toArray(), 'products' => $products], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post invoice
     *
     * @Post("/post_invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "customer_id":"5", "invoice_date":"2015-11-11","sales_person_id":"2","status":"status","total":"10.00","tax_amount":"01.10","grand_total":"11.10","discount":1.2,"final_price":9.85,"invoice_prefix":"I00","invoice_start_number":"0"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postInvoice(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'customer_id' => $request->input('customer_id'),
            'invoice_date' => $request->input('invoice_date'),
            'sales_person_id' => $request->input('sales_person_id'),
            'status' => $request->input('status'),
            'grand_total' => $request->input('grand_total'),
            'tax_amount' => $request->input('tax_amount'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
            'total' => $request->input('total'),
            'payment_term' => $request->input('payment_term'),
            'invoice_prefix' => $request->input('invoice_prefix'),
            'invoice_start_number' => $request->input('invoice_start_number'),
        );
        $rules = array(
            'customer_id' => 'required',
            'invoice_date' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'status' => 'required',
            'grand_total' => 'required',
            'tax_amount' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
            'total' => 'required',
            'payment_term' => "required",
            'invoice_start_number' => "required",
            'invoice_prefix' => "required"
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $total_fields = Invoice::whereNull('deleted_at')->orWhereNotNull('deleted_at')->orderBy('id', 'desc')->first();
            $invoice_no = $request->input('invoice_prefix') . ($request->input('invoice_start_number') + (isset($total_fields) ? $total_fields->id : 0) + 1);
            $exp_date = date('Y-m-d', strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));


            $invoice = new Invoice($request->only('customer_id', 'invoice_date', 'payment_term',
                'sales_person_id', 'sales_team_id', 'status', 'total',
                'tax_amount', 'grand_total','final_price','discount'));
            $invoice->invoice_number = $invoice_no;
            $invoice->unpaid_amount = $request->grand_total;
            $invoice->due_date = isset($request->due_date) ? $request->due_date : strtotime($exp_date);
            $invoice->user_id = Sentinel::getUser()->id;
            $invoice->save();

            InvoiceProduct::where('invoice_id', $invoice->id)->delete();

            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $invoiceProduct = new InvoiceProduct();
                        $invoiceProduct->invoice_id = $invoice->id;
                        $invoiceProduct->product_id = $item;
                        $invoiceProduct->product_name = $request->product_name[$key];
                        $invoiceProduct->description = $request->description[$key];
                        $invoiceProduct->quantity = $request->quantity[$key];
                        $invoiceProduct->price = $request->price[$key];
                        $invoiceProduct->sub_total = $request->sub_total[$key];
                        $invoiceProduct->save();
                    }
                }
            }

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit invoice
     *
     * @Post("/edit_invoice")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "invoice_id":"1","customer_id":"5", "invoice_date":"2015-11-11","sales_person_id":"2","status":"status","total":"10.00","tax_total":"01.10","grand_total":"11.10","discount":"0.10","final_price":"9.10"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editInvoice(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'invoice_id' => $request->input('invoice_id'),
            'customer_id' => $request->input('customer_id'),
            'invoice_date' => $request->input('invoice_date'),
            'sales_person_id' => $request->input('sales_person_id'),
            'status' => $request->input('status'),
            'grand_total' => $request->input('grand_total'),
            'total' => $request->input('total'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
            'payment_term' => $request->input('payment_term'),
        );
        $rules = array(
            'invoice_id' => 'required',
            'customer_id' => 'required',
            'invoice_date' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'status' => 'required',
            'grand_total' => 'required',
            'total' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
            'payment_term' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::find($request->invoice_id);

            $exp_date = date('m/d/Y', strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));

            $payments = InvoiceReceivePayment::where('invoice_id', $invoice->id);

            $invoice->unpaid_amount = $request->grand_total - (($payments->count() > 0) ? $payments->sum('payment_received') : 0);
            $invoice->due_date = isset($request->due_date) ? $request->due_date : strtotime($exp_date);
            $invoice->update($request->only('customer_id', 'invoice_date', 'payment_term',
                'sales_person_id', 'sales_team_id', 'status', 'total','final_price','discount',
                'tax_amount', 'grand_total'));
            InvoiceProduct::where('invoice_id', $invoice->id)->delete();

            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $invoiceProduct = new InvoiceProduct();
                        $invoiceProduct->invoice_id = $invoice->id;
                        $invoiceProduct->product_id = $item;
                        $invoiceProduct->product_name = $request->product_name[$key];
                        $invoiceProduct->description = $request->description[$key];
                        $invoiceProduct->quantity = $request->quantity[$key];
                        $invoiceProduct->price = $request->price[$key];
                        $invoiceProduct->sub_total = $request->sub_total[$key];
                        $invoiceProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete invoice
     *
     * @Post("/delete_invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "invoice_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteInvoice(Request $request)
    {
        $data = array(
            'invoice_id' => $request->input('invoice_id'),
        );
        $rules = array(
            'invoice_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $invoice = Invoice::find($request->invoice_id);
            $invoice->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get all invoice_payment
     *
     * @Get("/invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "invoices": {
    {
    "id": 1,
    "payment_number": "P002",
    "payment_received": "1525.26",
    "payment_method": "Paypal",
    "payment_date": "2015-11-11",
    "customer": "Customer Name",
    "person": "Person Name"
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function invoicePayment()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $invoice_payments = InvoiceReceivePayment::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->with('invoice.customer', 'invoice.salesPerson')
            ->get()->map(function ($ip) {
                return [
                    'id' => $ip->id,
                    'payment_number' => $ip->payment_number,
                    'payment_received' => $ip->payment_received,
                    'invoice_number' => $ip->invoice->invoice_number,
                    'payment_method' => $ip->payment_method,
                    'payment_date' => $ip->payment_date,
                    'customer' => $ip->invoice->customer->full_name,
                    'salesperson' => $ip->invoice->salesPerson->full_name
                ];
            })->toArray();

        return response()->json(['invoice_payments' => $invoice_payments], 200);
    }


    /**
     * Get all leads
     *
     * @Get("/leads")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "leads": {
    {
    "id": 1,
    "register_time": "2015-12-22",
    "opportunity": "1.2",
    "contact_name": "Contact name",
    "email": "dsad@asd.com",
    "phone": "456469465",
    "salesteam": "Test team",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function leads()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $leads = Lead::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('country', 'salesteam')
            ->get()
            ->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'register_time' => $lead->register_time,
                    'opportunity' => $lead->opportunity,
                    'contact_name' => $lead->contact_name,
                    'country' => $lead->country->name,
                    'email' => $lead->email,
                    'phone' => $lead->phone,
                    'salesteam' => $lead->salesTeam->salesteam
                ];
            })->toArray();

        return response()->json(['leads' => $leads], 200);
    }

    /**
     * Get lead item
     *
     * @Get("/lead")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "lead_id":"1"}),
     *       @Response(200, body={"invoice": {
    "id" : 1,
    "opportunity" : "Lead",
    "company_name" : "sdfsdf sdf",
    "customer_id" : 1,
    "address" : "sd fsdfd",
    "country_id" : 1,
    "state_id" : 43,
    "city_id" : 5914,
    "sales_person_id" : 1,
    "sales_team_id" : 1,
    "contact_name" : "sdfsdf sdf sdf ",
    "title" : "Doctor",
    "email" : "user@crm.com",
    "function" : "asdasd sad asd ",
    "phone" : "1545",
    "mobile" : "545",
    "fax" : "1545",
    "tags" : "2,4",
    "priority" : "Low",
    "internal_notes" : "asd asd asd ",
    "assigned_partner_id" : 0,
    "user_id" : 1,
    "created_at" : "2015-12-22 19:56:54",
    "updated_at" : "2015-12-22 19:56:54",
    "deleted_at" : null
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function lead(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'lead_id' => $request->input('lead_id'),
        );
        $rules = array(
            'lead_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $lead = Lead::find($request->lead_id);
            return response()->json(['lead' => $lead->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post lead
     *
     * @Post("/post_lead")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity":"125.5", "email":"test@test.com","customer_id":"12","sales_team_id":"1","tags":"Softwae","country_id":"15"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postLead(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer_id' => $request->input('customer_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'sales_person_id' => $request->input('sales_person_id'),
        );
        $rules = array(
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer_id' => 'required',
            'sales_team_id' => 'required',
            'sales_person_id' => "required"
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $this->leadRepository->store($request->all());

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit lead
     *
     * @Post("/edit_lead")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo","lead_id":1, "opportunity":"125.5", "email":"test@test.com","customer_id":"12","sales_team_id":"1","tags":"Softwae","country_id":"15"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editLead(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'lead_id' => $request->input('lead_id'),
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer_id' => $request->input('customer_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'sales_person_id' => $request->input('sales_person_id'),
        );
        $rules = array(
            'lead_id' => 'required',
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer_id' => 'required',
            'sales_team_id' => 'required',
            'sales_person_id' => "required"
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $lead = Lead::find($request->lead_id);
            $lead->tags = implode(',', $request->get('tags', []));
            $lead->update($request->except('tags', 'lead_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete lead
     *
     * @Post("/delete_lead")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "lead_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteLead(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'lead_id' => $request->input('lead_id'),
        );
        $rules = array(
            'lead_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $lead = Lead::find($request->lead_id);
            $lead->delete();
            return response()->json(['success' => "success"], 200);
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
    public function leadCall(Request $request)
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
    public function postLeadCall(Request $request)
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
            $call = $lead->calls()->create($request->all(), ['user_id' => $this->user->id]);
            $call->user_id = $this->user->id;
            $call->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editLeadCall(Request $request)
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
            $call->update($request->except('call_id'));

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
    public function deleteLeadCall(Request $request)
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
    public function meetings()
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
    public function meeting(Request $request)
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
            $meeting = Meeting::find($request->meeting_id);
            return response()->json(['meeting' => $meeting->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function postMeeting(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'meeting_subject' => $request->input('meeting_subject'),
            'starting_date' => $request->input('starting_date'),
            'ending_date' => $request->input('ending_date'),
        );
        $rules = array(
            'meeting_subject' => 'required',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $request->merge([
                'attendees' => implode(',', $request->get('attendees', []))
            ]);

            $user = Sentinel::getUser();
            $user->meetings()->create($request->all());

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editMeeting(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'meeting_id' => $request->input('meeting_id'),
            'meeting_subject' => $request->input('meeting_subject'),
            'starting_date' => $request->input('starting_date'),
            'ending_date' => $request->input('ending_date'),
        );
        $rules = array(
            'meeting_id' => 'required',
            'meeting_subject' => 'required',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $meeting = Meeting::find($request->meeting_id);
            $meeting->attendees = implode(',', $request->get('attendees', []));
            $meeting->update($request->except('attendees', 'meeting_id'));

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
    public function deleteMeeting(Request $request)
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

    /**
     * Get all opportunity call
     *
     * @Get("/opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity_id":"1"}),
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
    public function opportunityCall(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $opportunity = Opportunity::find($request->opportunity_id);
        $calls = $opportunity->calls()
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
     * Post opportunity call
     *
     * @Post("/post_opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "opportunity_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postOpportunityCall(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity_id' => $request->input('opportunity_id'),
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'date' => 'required|date',
            'opportunity_id' => "required",
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = Opportunity::find($request->opportunity_id);
            $call = $opportunity->calls()->create($request->all(), ['user_id' => $this->user->id]);
            $call->user_id = $this->user->id;
            $call->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit opportunity call
     *
     * @Post("/edit_opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "opportunity_id":"1","lead_id":"1","date":"2015-10-11", "call_summary":"call summary","company_id":"1","resp_staff_id":"12"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editOpportunityCall(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'call_id' => $request->input('call_id'),
            'opportunity_id' => $request->input('opportunity_id'),
            'date' => $request->input('date'),
            'call_summary' => $request->input('call_summary'),
            'company_id' => $request->input('company_id'),
            'resp_staff_id' => $request->input('resp_staff_id'),
        );
        $rules = array(
            'call_id' => 'required',
            'opportunity_id' => "required",
            'date' => 'required|date',
            'call_summary' => 'required',
            'company_id' => 'required',
            'resp_staff_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $call = Call::find($request->call_id);
            $call->update($request->except('call_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete opportunity call
     *
     * @Post("/delete_opportunity_call")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "call_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteOpportunityCall(Request $request)
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
    public function opportunities()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $opportunities = Opportunity::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('salesteam', 'company', 'calls', 'meetings')
            ->get()
            ->map(function ($opportunity) {
                return [
                    'id' => $opportunity->id,
                    'opportunity' => $opportunity->opportunity,
                    'company' => isset($opportunity->company) ? $opportunity->company->name : '',
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
    public function opportunity(Request $request)
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
            $opportunity = Opportunity::find($request->opportunity_id);
            return response()->json(['opportunity' => $opportunity->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function postOpportunity(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer' => $request->input('customer'),
            'sales_team_id' => $request->input('sales_team_id'),
            'next_action' => $request->input('next_action'),
            'expected_closing' => $request->input('expected_closing'),
        );
        $rules = array(
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer' => 'required',
            'sales_team_id' => 'required',
            'next_action' => 'required',
            'expected_closing' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = new Opportunity($request->all());
            if (isset($request->tags)) {
                $opportunity->tags = implode(',', $request->tags);
            }
            $opportunity->register_time = strtotime(date('d F Y g:i a'));
            $opportunity->ip_address = $request->ip();

            $this->user->opportunities()->save($opportunity);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editOpportunity(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'opportunity_id' => $request->input('opportunity_id'),
            'opportunity' => $request->input('opportunity'),
            'email' => $request->input('email'),
            'customer' => $request->input('customer'),
            'sales_team_id' => $request->input('sales_team_id'),
            'next_action' => $request->input('next_action'),
            'expected_closing' => $request->input('expected_closing'),
        );
        $rules = array(
            'opportunity_id' => 'required',
            'opportunity' => 'required',
            'email' => 'required|email',
            'customer' => 'required',
            'sales_team_id' => 'required',
            'next_action' => 'required',
            'expected_closing' => 'required'
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $opportunity = Opportunity::find($request->opportunity_id);
            if (isset($request->tags)) {
                $opportunity->tags = implode(',', $request->tags);
            }
            $opportunity->update($request->except('tags', 'opportunity_id'));

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
    public function deleteOpportunity(Request $request)
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
    public function opportunityMeeting(Request $request)
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
    public function postOpportunityMeeting(Request $request)
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
            $opportunity->meetings()->create($request->all(), ['user_id' => $this->user->id]);
            $opportunity->user_id = $this->user->id;
            $opportunity->save();

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editOpportunityMeeting(Request $request)
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
            $meeting->update($request->except('attendees', 'meeting_id'));

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
    public function deleteOpportunityMeeting(Request $request)
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


    /**
     * Get all products
     *
     * @Get("/products")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "products": {
    {
    "id": 1,
    "product_name": "product name",
    "name": "category",
    "product_type": "Type",
    "status": "1",
    "quantity_on_hand": "12",
    "quantity_available": "52"
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function products()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $products = Product::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('category')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'product_name' => $p->product_name,
                    'name' => $p->category->name,
                    'product_type' => $p->product_type,
                    'status' => $p->status,
                    'quantity_on_hand' => $p->quantity_on_hand,
                    'quantity_available' => $p->quantity_available,
                ];
            })->toArray();

        return response()->json(['products' => $products], 200);
    }

    /**
     * Get product item
     *
     * @Get("/product")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "product_id":"1"}),
     *       @Response(200, body={"product": {
    "id" : 1,
    "product_name" : "product",
    "product_image" : "",
    "category_id" : 1,
    "product_type" : "Consumable",
    "status" : "In Development",
    "quantity_on_hand" : 12,
    "quantity_available" : 22,
    "sale_price" : 1.0,
    "description" : "sdfdsfsdf",
    "description_for_quotations" : "sdfsdfsdfsdf",
    "user_id" : 1,
    "created_at" : "2015-12-23 16:58:51",
    "updated_at" : "2015-12-26 07:24:51",
    "deleted_at" : null
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function product(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_id' => $request->input('product_id'),
        );
        $rules = array(
            'product_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $product = Product::find($request->product_id);
            return response()->json(['product' => $product->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post product
     *
     * @Post("/post_product")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postProduct(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_name' => $request->input('product_name'),
            'sale_price' => $request->input('sale_price'),
            'description' => $request->input('description'),
            'quantity_on_hand' => $request->input('quantity_on_hand'),
            'quantity_available' => $request->input('quantity_available'),
        );
        $rules = array(
            'product_name' => "required",
            'sale_price' => "required",
            'description' => "required",
            'quantity_on_hand' => "required",
            'quantity_available' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $product = new Product($request->all());
            $this->user->products()->save($product);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit product
     *
     * @Post("/edit_product")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "product_id":"1","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editProduct(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_id' => $request->input('product_id'),
            'product_name' => $request->input('product_name'),
            'sale_price' => $request->input('sale_price'),
            'description' => $request->input('description'),
            'quantity_on_hand' => $request->input('quantity_on_hand'),
            'quantity_available' => $request->input('quantity_available'),
        );
        $rules = array(
            'product_id' => 'required',
            'product_name' => "required",
            'sale_price' => "required",
            'description' => "required",
            'quantity_on_hand' => "required",
            'quantity_available' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $product = Product::find($request->product_id);
            $product->update($request->except('product_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete product
     *
     * @Post("/delete_product")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "product_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteProduct(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'product_id' => $request->input('product_id'),
        );
        $rules = array(
            'product_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $product = Product::find($request->product_id);
            $product->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get all qtemplates
     *
     * @Get("/qtemplates")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "qtemplates": {
    {
    "id": 1,
    "quotation_template": "product name",
    "quotation_duration": "10",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function qtemplates()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $qtemplates = Qtemplate::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })->select('id', 'quotation_template', 'quotation_duration')->get()->toArray();

        return response()->json(['qtemplates' => $qtemplates], 200);
    }

    /**
     * Get qtemplate item
     *
     * @Get("/qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "qtemplate_id":"1"}),
     *       @Response(200, body={"qtemplate": {
    "id" : 1,
    "quotation_template" : "testaa",
    "quotation_duration" : 19,
    "immediate_payment" : 0,
    "terms_and_conditions" : "sd f sdf 22",
    "total" : 2553.0,
    "tax_amount" : 408.48,
    "grand_total" : 2961.48,
    "user_id" : 1,
    "created_at" : "2015-12-23 18:45:58",
    "updated_at" : "2015-12-23 18:46:21",
    "deleted_at" : null
    },"products": {
    "product" : "product",
    "description" : "description",
    "quantity" : 3,
    "unit_price" : 1.95,
    "taxes" : 1.55,
    "subtotal" : 195.36
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function qtemplate(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'qtemplate_id' => $request->input('qtemplate_id'),
        );
        $rules = array(
            'qtemplate_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $qtemplate = Qtemplate::find($request->qtemplate_id);
            $products = array();
            if ($qtemplate->products->count() > 0) {
                $sales_tax = 0;
                $values = UserSetting::whereHas('user', function ($q) {
                    $q->where(function ($query) {
                        $query
                            ->orWhere('id', $this->user->parent->id)
                            ->orWhere('users.user_id', $this->user->parent->id);
                    });
                })->where('key','sales_tax');
                if ($values->count()==1) {
                    $sales_tax = $values->first()->value;
                }

                foreach ($qtemplate->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * $sales_tax / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['qtemplate' => $qtemplate->toArray(),'products'=>$products], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post qtemplate
     *
     * @Post("/post_qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11","total":"10.00","tax_amount":"1.11","grand_total":"11.11"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postQtemplate(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_template' => $request->input('quotation_template'),
            'quotation_duration' => $request->input('quotation_duration'),
            'total' => $request->input('total'),
            'tax_amount' => $request->input('tax_amount'),
            'grand_total' => $request->input('grand_total'),
        );
        $rules = array(
            'quotation_template' => 'required',
            'quotation_duration' => "required",
            'total' => "required",
            'tax_amount' => "required",
            'grand_total' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $qtemplate = new Qtemplate($request->all());
            $this->user->qtemplates()->save($qtemplate);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit qtemplate
     *
     * @Post("/edit_qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "qtemplate_id":"1","product_name":"product name", "sale_price":"15.2","description":"sadsadsd","quantity_on_hand":"12","quantity_available":"11","total":"10.00","tax_amount":"1.11","grand_total":"11.11"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editQtemplate(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'qtemplate_id' => $request->input('qtemplate_id'),
            'quotation_template' => $request->input('quotation_template'),
            'quotation_duration' => $request->input('quotation_duration'),
            'total' => $request->input('total'),
            'tax_amount' => $request->input('tax_amount'),
            'grand_total' => $request->input('grand_total'),
        );
        $rules = array(
            'qtemplate_id' => 'required',
            'quotation_template' => 'required',
            'quotation_duration' => "required",
            'total' => "required",
            'tax_amount' => "required",
            'grand_total' => "required",
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $qtemplate = Qtemplate::find($request->qtemplate_id);
            $qtemplate->update($request->except('qtemplate_id'));

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete qtemplate
     *
     * @Post("/delete_qtemplate")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "qtemplate_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteQtemplate(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'qtemplate_id' => $request->input('qtemplate_id'),
        );
        $rules = array(
            'qtemplate_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $qtemplate = Qtemplate::find($request->qtemplate_id);
            $qtemplate->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get all quotations
     *
     * @Get("/quotations")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "quotations": {
    {
    "id": 1,
    "quotations_number": "4545",
    "date": "2015-11-11",
    "customer": "customer name",
    "person": "person name",
    "final_price": "12",
    "status": "1",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function quotations()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $quotations = QuotationSaleorder::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('user', 'customer')
            ->get()
            ->map(function ($quotation) {
                return [
                    'id' => $quotation->id,
                    'quotations_number' => $quotation->quotations_number,
                    'date' => $quotation->date,
                    'customer' => $quotation->customer->full_name,
                    'person' => $quotation->user->full_name,
                    'final_price' => $quotation->final_price,
                    'status' => $quotation->status
                ];
            })->toArray();

        return response()->json(['quotations' => $quotations], 200);
    }

    /**
     * Get quotation item
     *
     * @Get("/quotation")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "quotation_id":"1"}),
     *       @Response(200, body={"quotation": {
    "id" : 1,
    "quotations_number" : "Q0001",
    "customer_id" : 3,
    "qtemplate_id" : 0,
    "date" : "08.12.2015. 00:00",
    "exp_date" : "30.12.2015.",
    "payment_term" : "10",
    "sales_person_id" : 2,
    "sales_team_id" : 1,
    "terms_and_conditions" : "dff dfg dfg",
    "status" : "Draft Quotation",
    "total" : 333.0,
    "tax_amount" : 53.28,
    "grand_total" : 386.28,
    "discount" : 11.28,
    "final_price" : 289.28,
    "user_id" : 1,
    "created_at" : "2015-12-23 18:39:12",
    "updated_at" : "2015-12-23 18:39:12",
    "deleted_at" : null
    },"products": {
    "product" : "product",
    "description" : "description",
    "quantity" : 3,
    "unit_price" : 1.95,
    "taxes" : 1.55,
    "subtotal" : 195.36
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function quotation(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_id' => $request->input('quotation_id'),
        );
        $rules = array(
            'quotation_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $quotation = Quotation::find($request->quotation_id);
            $products = array();
            if ($quotation->products->count() > 0) {
                $sales_tax = 0;
                $values = UserSetting::whereHas('user', function ($q) {
                    $q->where(function ($query) {
                        $query
                            ->orWhere('id', $this->user->parent->id)
                            ->orWhere('users.user_id', $this->user->parent->id);
                    });
                })->where('key','sales_tax');
                if ($values->count()==1) {
                    $sales_tax = $values->first()->value;
                }

                foreach ($quotation->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * $sales_tax / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['quotation' => $quotation->toArray(), '$products'=>$products], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post quotation
     *
     * @Post("/post_quotation")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person_id":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25","quotation_prefix":"Q00","quotation_start_number":"0"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postQuotation(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'customer_id' => $request->input('customer_id'),
            'date' => $request->input('date'),
            'qtemplate_id' => $request->input('qtemplate_id'),
            'payment_term' => $request->input('payment_term'),
            'sales_person_id' => $request->input('sales_person_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'grand_total' => $request->input('grand_total'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
            'quotation_prefix' => $request->input('quotation_prefix'),
            'quotation_start_number' => $request->input('quotation_start_number'),
        );
        $rules = array(
            'customer_id' => 'required',
            'date' => 'required',
            'exp_date' => 'date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
            'quotation_prefix' => 'required',
            'quotation_start_number' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $total_fields = Quotation::whereNull('deleted_at')->orWhereNotNull('deleted_at')->orderBy('id', 'desc')->first();
            $quotation_no = $request->quotation_prefix . ($request->quotation_start_number + (isset($total_fields) ? $total_fields->id : 0) + 1);
            $exp_date = date('Y-m-d', strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));


            $quotation = new Quotation($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));
            $quotation->quotations_number = $quotation_no;
            $quotation->exp_date = isset($request->exp_date) ? $request->exp_date : strtotime($exp_date);
            $quotation->user_id = $this->user->id;
            $quotation->save();

            QuotationProduct::where('quotation_id', $quotation->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $quotationProduct = new QuotationProduct();
                        $quotationProduct->quotation_id = $quotation->id;
                        $quotationProduct->product_id = $item;
                        $quotationProduct->product_name = $request->product_name[$key];
                        $quotationProduct->description = $request->description[$key];
                        $quotationProduct->quantity = $request->quantity[$key];
                        $quotationProduct->price = $request->price[$key];
                        $quotationProduct->sub_total = $request->sub_total[$key];
                        $quotationProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit quotation
     *
     * @Post("/edit_quotation")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "quotation_id":"1","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editQuotation(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_id' => $request->input('quotation_id'),
            'customer_id' => $request->input('customer_id'),
            'date' => $request->input('date'),
            'qtemplate_id' => $request->input('qtemplate_id'),
            'payment_term' => $request->input('payment_term'),
            'sales_person_id' => $request->input('sales_person_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'grand_total' => $request->input('grand_total'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
        );
        $rules = array(
            'quotation_id' => 'required',
            'customer_id' => 'required',
            'exp_date' => 'date',
            'date' => 'required|date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $quotation = Quotation::find($request->quotation_id);

            $quotation->update($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));

            QuotationProduct::where('quotation_id', $quotation->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $quotationProduct = new QuotationProduct();
                        $quotationProduct->quotation_id = $quotation->id;
                        $quotationProduct->product_id = $item;
                        $quotationProduct->product_name = $request->product_name[$key];
                        $quotationProduct->description = $request->description[$key];
                        $quotationProduct->quantity = $request->quantity[$key];
                        $quotationProduct->price = $request->price[$key];
                        $quotationProduct->sub_total = $request->sub_total[$key];
                        $quotationProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete quotation
     *
     * @Post("/delete_quotation")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "quotation_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteQuotation(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'quotation_id' => $request->input('quotation_id'),
        );
        $rules = array(
            'quotation_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $quotation = Quotation::find($request->quotation_id);
            $quotation->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


    /**
     * Get all sales orders
     *
     * @Get("/sales_orders")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "salesorders": {
    {
    "id": 1,
    "quotations_number": "product name",
    "date": "2015-11-11",
    "customer": "customer name",
    "person": "sales person name",
    "final_price": "12.53",
    "status": "1",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function salesOrders()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $salesorder = Saleorder::whereHas('user', function ($q) {
            $q->where(function ($query) {
                $query
                    ->orWhere('id', $this->user->parent->id)
                    ->orWhere('users.user_id', $this->user->parent->id);
            });
        })
            ->with('user', 'customer')
            ->get()
            ->map(function ($quotation) {
                return [
                    'id' => $quotation->id,
                    'quotations_number' => $quotation->quotations_number,
                    'date' => $quotation->date,
                    'customer' => $quotation->customer->full_name,
                    'person' => $quotation->user->full_name,
                    'final_price' => $quotation->final_price,
                    'status' => $quotation->status
                ];
            })->toArray();

        return response()->json(['salesorders' => $salesorder], 200);
    }

    /**
     * Get salesorder item
     *
     * @Get("/salesorder")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "salesorder_id":"1"}),
     *       @Response(200, body={"salesorder": {
    "id" : 1,
    "sale_number" : "S0001",
    "customer_id" : 3,
    "qtemplate_id" : 0,
    "date" : "15.12.2015.",
    "exp_date" : "15.12.2015.",
    "payment_term" : "15",
    "sales_person_id" : 2,
    "sales_team_id" : 1,
    "terms_and_conditions" : "drtret",
    "status" : "Draft sales order",
    "total" : 1221.0,
    "tax_amount" : 195.36,
    "grand_total" : 1416.36,
    "discount" : 11.28,
    "final_price" : 289.28,
    "user_id" : 1,
    "created_at" : "2015-12-23 17:12:39",
    "updated_at" : "2015-12-23 17:12:39",
    "deleted_at" : null
    },"products": {
    "product" : "product",
    "description" : "description",
    "quantity" : 3,
    "unit_price" : 1.95,
    "taxes" : 1.55,
    "subtotal" : 195.36
    }}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function salesorder(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'salesorder_id' => $request->input('salesorder_id'),
        );
        $rules = array(
            'salesorder_id' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $salesorder = Saleorder::find($request->salesorder_id);
            $products = array();
            if ($salesorder->products->count() > 0) {
                $sales_tax = 0;
                $values = UserSetting::whereHas('user', function ($q) {
                    $q->where(function ($query) {
                        $query
                            ->orWhere('id', $this->user->parent->id)
                            ->orWhere('users.user_id', $this->user->parent->id);
                    });
                })->where('key','sales_tax');
                if ($values->count()==1) {
                    $sales_tax = $values->first()->value;
                }
                foreach ($salesorder->products as $index => $variants) {
                    $products[] = ['product' => $variants->product_name,
                        'description' => $variants->description,
                        'quantity' => $variants->quantity,
                        'unit_price' => $variants->price,
                        'taxes' => number_format($variants->quantity * $variants->price * $sales_tax / 100, 2,
                            '.', ''),
                        'subtotal' => $variants->sub_total];
                }
            }
            return response()->json(['salesorder' => $salesorder->toArray(),'products' => $products], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Post Sales Order
     *
     * @Post("/post_sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person_id":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25","sales_prefix":"S00","sales_start_number":"0"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function postSalesOrder(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'customer_id' => $request->input('customer_id'),
            'date' => $request->input('date'),
            'qtemplate_id' => $request->input('qtemplate_id'),
            'payment_term' => $request->input('payment_term'),
            'sales_person_id' => $request->input('sales_person_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'grand_total' => $request->input('grand_total'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
            "sales_prefix" => $request->input('sales_prefix'),
            "sales_start_number" => $request->input('sales_start_number'),
        );
        $rules = array(
            'customer_id' => 'required',
            'date' => 'required|date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
            'sales_prefix' => 'required',
            'sales_start_number' => 'required',
            'exp_date' => 'date',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {

            $total_fields = Saleorder::whereNull('deleted_at')->orWhereNotNull('deleted_at')->orderBy('id', 'desc')->first();
            $sale_no = $request->input('sales_prefix') . ($request->input('sales_start_number') + (isset($total_fields) ? $total_fields->id : 0) + 1);
            $exp_date = date('Y-m-d', strtotime(' + ' . isset($request->payment_term) ? $request->payment_term : 0 . ' days'));

            $saleorder = new Saleorder($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));
            $saleorder->sale_number = $sale_no;
            $saleorder->exp_date = isset($request->exp_date) ? $request->exp_date : strtotime($exp_date);
            $saleorder->user_id = $this->user->id;
            $saleorder->save();

            SaleorderProduct::where('order_id', $saleorder->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $saleorderProduct = new SaleorderProduct();
                        $saleorderProduct->order_id = $saleorder->id;
                        $saleorderProduct->product_id = $item;
                        $saleorderProduct->product_name = $request->product_name[$key];
                        $saleorderProduct->description = $request->description[$key];
                        $saleorderProduct->quantity = $request->quantity[$key];
                        $saleorderProduct->price = $request->price[$key];
                        $saleorderProduct->sub_total = $request->sub_total[$key];
                        $saleorderProduct->save();
                    }
                }
            }

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Edit quotation
     *
     * @Post("/edit_sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *       @Request({"token": "foo", "sales_order_id":"1","customer_id":"1", "date":"2015-11-11","qtemplate_id":"1","payment_term":"term","sales_person_id":"1","sales_team_id":"1","grand_total":"12.5","discount":"10.2","final_price":"10.25"}),
     *       @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *    })
     * })
     */
    public function editSalesOrder(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'sales_order_id' => $request->input('sales_order_id'),
            'customer_id' => $request->input('customer_id'),
            'date' => $request->input('date'),
            'qtemplate_id' => $request->input('qtemplate_id'),
            'payment_term' => $request->input('payment_term'),
            'sales_person_id' => $request->input('sales_person_id'),
            'sales_team_id' => $request->input('sales_team_id'),
            'grand_total' => $request->input('grand_total'),
            'discount' => $request->input('discount'),
            'final_price' => $request->input('final_price'),
        );
        $rules = array(
            'sales_order_id' => 'required',
            'customer_id' => 'required',
            'date' => 'required|date',
            'exp_date' => 'date',
            'qtemplate_id' => 'required',
            'payment_term' => 'required',
            'sales_person_id' => 'required',
            'sales_team_id' => 'required',
            'grand_total' => 'required',
            'discount' => 'required',
            'final_price' => 'required',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $sales_order = Saleorder::find($request->sales_order_id);
            $sales_order->update($request->only('customer_id', 'qtemplate_id', 'date',
                'exp_date', 'payment_term', 'sales_person_id', 'sales_team_id', 'terms_and_conditions', 'status', 'total',
                'tax_amount', 'grand_total','discount','final_price'));

            SaleorderProduct::where('order_id', $sales_order->id)->delete();
            if (!empty($request->product_id)) {
                foreach ($request->product_id as $key => $item) {
                    if ($item != "" && $request->product_name[$key] != "" && $request->description[$key] != "" &&
                        $request->quantity[$key] != "" && $request->price[$key] != "" && $request->sub_total[$key] != ""
                    ) {
                        $saleorderProduct = new SaleorderProduct();
                        $saleorderProduct->order_id = $sales_order->id;
                        $saleorderProduct->product_id = $item;
                        $saleorderProduct->product_name = $request->product_name[$key];
                        $saleorderProduct->description = $request->description[$key];
                        $saleorderProduct->quantity = $request->quantity[$key];
                        $saleorderProduct->price = $request->price[$key];
                        $saleorderProduct->sub_total = $request->sub_total[$key];
                        $saleorderProduct->save();
                    }
                }
            }
            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }

    /**
     * Delete quotation
     *
     * @Post("/delete_sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo", "sales_order_id":"1"}),
     *      @Response(200, body={"success":"success"}),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function deleteSalesOrder(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $data = array(
            'sales_order_id' => $request->input('sales_order_id'),
        );
        $rules = array(
            'sales_order_id' => 'required|integer',
        );
        $validator = Validator::make($data, $rules);
        if ($validator->passes()) {
            $sales_order = Saleorder::find($request->sales_order_id);
            $sales_order->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


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
     * })
     */
    public function salesTeams()
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
    public function salesteam(Request $request)
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
            $salesteam = Salesteam::find($request->salesteam_id);
            return response()->json(['salesteam' => $salesteam->toArray()], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function postSalesTeam(Request $request)
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

            $salesteam = new Salesteam($request->all());
            $salesteam->team_members = implode(',', $request->get('team_members', []));
            $salesteam->register_time = strtotime(date('d F Y g:i a'));
            $salesteam->ip_address = $request->server('REMOTE_ADDR');
            $salesteam->quotations = ($request->quotations) ? $request->quotations : 0;
            $salesteam->leads = ($request->leads) ? $request->leads : 0;
            $salesteam->opportunities = ($request->opportunities) ? $request->opportunities : 0;
            $salesteam->status = ($request->status) ? $request->status : 0;
            $this->user->salesTeams()->save($salesteam);

            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editSalesTeam(Request $request)
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
            $salesteam->status = ($request->status) ? $request->status : 0;
            $salesteam->update($request->except('team_members', 'quotations', 'leads', 'opportunities', 'status', 'salesteam_id'));

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
    public function deleteSalesTeam(Request $request)
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
    public function staff()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $staff = Staff::whereHas('user', function ($q) {
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
                    'created_at' => $user->created_at->format('Y-m-d'),
                ];
            })->toArray();

        return response()->json(['staff' => $staff], 200);
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
    public function postStaff(Request $request)
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

            $user->user_id = Sentinel::getUser()->user_id;
            $user->save();

            Staff::create(array('user_id' => $user->id, 'belong_user_id' => Sentinel::getUser()->user_id));


            return response()->json(['success' => 'success'], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
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
    public function editStaff(Request $request)
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
            $staff = Staff::find($request->staff_id);
            $staff->update($request->except('staff_id', 'email'));

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
    public function deleteStaff(Request $request)
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
            $staff = Staff::find($request->staff_id);
            $staff->delete();
            return response()->json(['success' => "success"], 200);
        } else {
            return response()->json(['error' => 'not_valid_data'], 500);
        }
    }


}
