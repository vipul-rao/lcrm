<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationSaleorder;
use App\Models\Saleorder;
use App\Models\Staff;
use Dingo\Api\Routing\Helpers;
use Validator;
use JWTAuth;
use DB;

/**
 * Customer endpoints, can be accessed only with role "customer"
 *
 * @Resource("Customer", uri="/customer")
 */
class CustomerController extends Controller
{
    use Helpers;

    private $user;

    /**
     * Get all contract
     *
     * @Get("/contract")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "company": {
    {
    "id": 1,
    "start_date": "2015-11-12",
    "end_date": "2015-11-15",
    "description": "Description",
    "company": "Company name",
    "user": "User name",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function contract()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $customer = Customer::where('user_id', $this->user->id)->first();
        $company_id = $customer->company_id;
        $contracts = Contract::whereHas('user', function ($q) use ($company_id) {
            $q->where('company_id', $company_id);
        })
            ->with('company', 'user')
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'description' => $contract->description,
                    'company' => $contract->company->name,
                    'user' => $contract->responsible->full_name,
                ];
            })
            ->toArray();

        return response()->json(['contracts' => $contracts], 200);
    }

    /**
     * Get all invoice
     *
     * @Get("/invoice")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "invoices": {
    {
    "id": 1,
    "invoice_number": "I0056",
    "invoice_date": "2015-11-11",
    "customer": "Customer Name",
    "due_date": "2015-11-12",
    "grand_total": "15.2",
    "unpaid_amount": "15.2",
    "status": "Status",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function invoice()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $invoices = Invoice::whereHas('user', function ($q) {
            $q->where('customer_id', $this->user->id);
        })->with('customer')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'customer' => isset($invoice->customer) ? $invoice->customer->full_name : '',
                    'due_date' => $invoice->due_date,
                    'grand_total' => $invoice->grand_total,
                    'unpaid_amount' => $invoice->unpaid_amount,
                    'status' => $invoice->status,
                ];
            })->toArray();

        return response()->json(['invoices' => $invoices], 200);
    }

    /**
     * Get all quotation
     *
     * @Get("/quotation")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "quotations": {
    {
    "id": 1,
    "quotations_number": "Q002",
    "date": "2015-11-11",
    "customer": "customer name",
    "person": "person name",
    "grand_total": "12",
    "status": "Draft quotation",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function quotation()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $quotations = Quotation::whereHas('customer', function ($q) {
            $q->where('customer_id', $this->user->id);
        })
            ->with('user', 'customer')
            ->get()
            ->map(function ($quotation) {
                return [
                    'id' => $quotation->id,
                    'quotations_number' => $quotation->quotations_number,
                    'date' => $quotation->date,
                    'customer' => isset($quotation->customer) ?$quotation->customer->full_name : '',
                    'person' => isset($quotation->user) ?$quotation->user->full_name : '',
                    'grand_total' => $quotation->grand_total,
                    'status' => $quotation->status
                ];
            })->toArray();

        return response()->json(['quotations' => $quotations], 200);
    }

    /**
     * Get all sales order
     *
     * @Get("/sales_order")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"token": "foo"}),
     *      @Response(200, body={
    "salesorder": {
    {
    "id": 1,
    "sale_number": "S002",
    "date": "2015-11-11",
    "customer": "customer name",
    "person": "sales person name",
    "grand_total": "12.53",
    "status": "Draft sales order",
    }
    }
    }),
     *      @Response(500, body={"error":"not_valid_data"})
     *       })
     * })
     */
    public function salesOrder()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $sales_orders = Saleorder::whereHas('customer', function ($q) {
            $q->where('customer_id', $this->user->id);
        })->with('user', 'customer')
            ->get()
            ->map(function ($sales_order) {
                return [
                    'id' => $sales_order->id,
                    'sale_number' => $sales_order->sale_number,
                    'date' => $sales_order->date,
                    'customer' => isset($sales_order->customer) ?$sales_order->customer->full_name : '',
                    'person' => isset($sales_order->user) ?$sales_order->user->full_name : '',
                    'grand_total' => $sales_order->grand_total,
                    'status' => $sales_order->status
                ];
            })->toArray();

        return response()->json(['salesorder' => $sales_orders], 200);
    }

}
