<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['namespace' => 'App\Http\Controllers\Api'], function ($api) {
    $api->post('login', 'AuthController@login');

    //routes for user and staff
    $api->group(['prefix' => 'user', 'middleware' => 'api.user.staff'], function ($api) {
        $api->get('calendar', 'UserController@calendar');

        $api->get('countries', 'SettingsController@countries');
        $api->get('states', 'SettingsController@states');
        $api->get('cities', 'SettingsController@cities');

        $api->get('settings', 'SettingsController@settings');
        $api->post('update_settings', 'SettingsController@updateSettings');
*/
        /*$api->get('calls', 'UserController@calls');
        $api->get('call', 'UserController@call');
        $api->post('post_call', 'UserController@postCall');
        $api->post('edit_call', 'UserController@editCall');
        $api->post('delete_call', 'UserController@deleteCall');

        $api->get('categories', 'UserController@categories');
        $api->get('category', 'UserController@category');
        $api->post('post_category', 'UserController@postCategory');
        $api->post('edit_category', 'UserController@editCategory');
        $api->post('delete_category', 'UserController@deleteCategory');

        $api->get('companies', 'UserController@companies');
        $api->get('company', 'UserController@company');
        $api->post('post_company', 'UserController@postCompany');
        $api->post('edit_company', 'UserController@editCompany');
        $api->post('delete_company', 'UserController@deleteCompany');

        $api->get('contract', 'UserController@contract');
        $api->get('contracts', 'UserController@contracts');
        $api->post('post_contract', 'UserController@postContract');
        $api->post('edit_contract', 'UserController@editContract');
        $api->post('delete_contract', 'UserController@deleteContract');

        $api->get('customer', 'UserController@customer');
        $api->get('customers', 'UserController@customers');
        $api->post('post_customer', 'UserController@postCustomer');
        $api->post('edit_customer', 'UserController@editCustomer');
        $api->post('delete_customer', 'UserController@deleteCustomer');

        $api->get('invoice', 'UserController@invoice');
        $api->get('invoices', 'UserController@invoices');
        $api->post('post_invoice', 'UserController@postInvoice');
        $api->post('edit_invoice', 'UserController@editInvoice');
        $api->post('delete_invoice', 'UserController@deleteInvoice');

        $api->get('invoice_payment', 'UserController@invoicePayment');
        $api->get('post_invoice_payment', 'UserController@postInvoicePayment');

        $api->get('lead_call', 'UserController@leadCall');
        $api->post('post_lead_call', 'UserController@postLeadCall');
        $api->post('edit_lead_call', 'UserController@editLeadCall');
        $api->post('delete_lead_call', 'UserController@deleteLeadCall');

        $api->get('lead', 'UserController@lead');
        $api->get('leads', 'UserController@leads');
        $api->post('post_lead', 'UserController@postLead');
        $api->post('edit_lead', 'UserController@editLead');
        $api->post('delete_lead', 'UserController@deleteLead');

        $api->get('meeting', 'UserController@meeting');
        $api->get('meetings', 'UserController@meetings');
        $api->post('post_meeting', 'UserController@postMeeting');
        $api->post('edit_meeting', 'UserController@editMeeting');
        $api->post('delete_meeting', 'UserController@deleteMeeting');

        $api->get('opportunity_call', 'UserController@opportunityCall');
        $api->post('post_opportunity_call', 'UserController@postOpportunityCall');
        $api->post('edit_opportunity_call', 'UserController@editOpportunityCall');
        $api->post('delete_opportunity_call', 'UserController@deleteOpportunityCall');

        $api->get('opportunity', 'UserController@opportunity');
        $api->post('post_opportunity', 'UserController@postOpportunity');
        $api->post('edit_opportunity', 'UserController@editOpportunity');
        $api->post('delete_opportunity', 'UserController@deleteOpportunity');

        $api->get('opportunity_meeting', 'UserController@opportunityMeeting');
        $api->post('post_opportunity_meeting', 'UserController@postOpportunityMeeting');
        $api->post('edit_opportunity_meeting', 'UserController@editOpportunityMeeting');
        $api->post('delete_opportunity_meeting', 'UserController@deleteOpportunityMeeting');

        $api->get('product', 'UserController@product');
        $api->get('products', 'UserController@products');
        $api->post('post_product', 'UserController@postProduct');
        $api->post('edit_product', 'UserController@editProduct');
        $api->post('delete_product', 'UserController@deleteProduct');

        $api->get('qtemplate', 'UserController@qtemplate');
        $api->get('qtemplates', 'UserController@qtemplates');
        $api->post('post_qtemplate', 'UserController@postQtemplate');
        $api->post('edit_qtemplate', 'UserController@editQtemplate');
        $api->post('delete_qtemplate', 'UserController@deleteQtemplate');

        $api->get('quotation', 'UserController@quotation');
        $api->get('quotations', 'UserController@quotations');
        $api->post('post_quotation', 'UserController@postQuotation');
        $api->post('edit_quotation', 'UserController@editQuotation');
        $api->post('delete_quotation', 'UserController@deleteQuotation');

        $api->get('sales_order', 'UserController@salesOrder');
        $api->get('sales_orders', 'UserController@salesOrders');
        $api->post('post_sales_order', 'UserController@postSalesOrder');
        $api->post('edit_sales_order', 'UserController@editSalesOrder');
        $api->post('delete_sales_order', 'UserController@deleteSalesOrder');

        $api->get('salesteam', 'UserController@salesTeam');
        $api->get('salesteams', 'UserController@salesTeams');
        $api->post('post_salesteam', 'UserController@postSalesTeam');
        $api->post('edit_salesteam', 'UserController@editSalesTeam');
        $api->post('delete_salesteam', 'UserController@deleteSalesTeam');

        $api->get('staff', 'UserController@staff');
        $api->post('post_staff', 'UserController@postStaff');
        $api->post('edit_staff', 'UserController@editStaff');
        $api->post('delete_staff', 'UserController@deleteStaff');*/
/*
        $api->group(['prefix' => 'calls'], function ($api) {
            $api->get('/', 'CallController@index');
            $api->get('show', 'CallController@show');
            $api->post('create', 'CallController@store');
            $api->post('edit', 'CallController@update');
            $api->post('delete', 'CallController@destroy');
        });

        $api->group(['prefix' => 'categories'], function ($api) {
            $api->get('/', 'CategoryController@index');
            $api->get('show', 'CategoryController@show');
            $api->post('create', 'CategoryController@store');
            $api->post('edit', 'CategoryController@update');
            $api->post('delete', 'CategoryController@destroy');
        });

        $api->group(['prefix' => 'companies'], function ($api) {
            $api->get('/', 'CompanyController@index');
            $api->get('show', 'CompanyController@show');
            $api->post('create', 'CompanyController@store');
            $api->post('edit', 'CompanyController@update');
            $api->post('delete', 'CompanyController@destroy');
        });

        $api->group(['prefix' => 'customers'], function ($api) {
            $api->get('/', 'UserCustomerController@index');
            $api->get('show', 'UserCustomerController@show');
            $api->post('create', 'UserCustomerController@store');
            $api->post('edit', 'UserCustomerController@update');
            $api->post('delete', 'UserCustomerController@destroy');
        });

        $api->group(['prefix' => 'invoices'], function ($api) {
            $api->get('/', 'InvoiceController@index');
            $api->get('show', 'InvoiceController@show');
            $api->post('create', 'InvoiceController@store');
            $api->post('edit', 'InvoiceController@update');
            $api->post('delete', 'InvoiceController@destroy');
        });

        $api->group(['prefix' => 'invoice_payments'], function ($api) {
            $api->get('/', 'InvoicePaymentController@index');
            $api->get('show', 'InvoicePaymentController@show');
            $api->post('create', 'InvoicePaymentController@store');
        });

        $api->group(['prefix' => 'lead_call'], function ($api) {
            $api->get('/', 'LeadCallController@show');
            $api->post('create', 'LeadCallController@store');
            $api->post('edit', 'LeadCallController@update');
            $api->post('delete', 'LeadCallController@destroy');
        });

        $api->group(['prefix' => 'leads'], function ($api) {
            $api->get('/', 'LeadController@index');
            $api->get('show', 'LeadController@show');
            $api->post('create', 'LeadController@store');
            $api->post('edit', 'LeadController@update');
            $api->post('delete', 'LeadController@destroy');
        });

        $api->group(['prefix' => 'meetings'], function ($api) {
            $api->get('/', 'MeetingController@index');
            $api->get('show', 'MeetingController@show');
            $api->post('create', 'MeetingController@store');
            $api->post('edit', 'MeetingController@update');
            $api->post('delete', 'MeetingController@destroy');
        });

        $api->group(['prefix' => 'opportunity_calls'], function ($api) {
            $api->get('/', 'OpportunityCallController@index');
            $api->get('show', 'OpportunityCallController@show');
            $api->post('create', 'OpportunityCallController@store');
            $api->post('edit', 'OpportunityCallController@update');
            $api->post('delete', 'OpportunityCallController@destroy');
        });

        $api->group(['prefix' => 'opportunities'], function ($api) {
            $api->get('/', 'OpportunityController@index');
            $api->get('show', 'OpportunityController@show');
            $api->post('create', 'OpportunityController@store');
            $api->post('edit', 'OpportunityController@update');
            $api->post('delete', 'OpportunityController@destroy');
        });

        $api->group(['prefix' => 'opportunity_meetings'], function ($api) {
            $api->get('/', 'OpportunityMeetingController@index');
            $api->get('show', 'OpportunityMeetingController@show');
            $api->post('create', 'OpportunityMeetingController@store');
            $api->post('edit', 'OpportunityMeetingController@update');
            $api->post('delete', 'OpportunityMeetingController@destroy');
        });

        $api->group(['prefix' => 'products'], function ($api) {
            $api->get('/', 'ProductController@index');
            $api->get('show', 'ProductController@show');
            $api->post('create', 'ProductController@store');
            $api->post('edit', 'ProductController@update');
            $api->post('delete', 'ProductController@destroy');
        });

        $api->group(['prefix' => 'qtemplates'], function ($api) {
            $api->get('/', 'QTemplateController@index');
            $api->get('show', 'QTemplateController@show');
            $api->post('create', 'QTemplateController@store');
            $api->post('edit', 'QTemplateController@update');
            $api->post('delete', 'QTemplateController@destroy');
        });

        $api->group(['prefix' => 'quotations'], function ($api) {
            $api->get('/', 'QuotationController@index');
            $api->get('show', 'QuotationController@show');
            $api->post('create', 'QuotationController@store');
            $api->post('edit', 'QuotationController@update');
            $api->post('delete', 'QuotationController@destroy');
        });

        $api->group(['prefix' => 'sales_orders'], function ($api) {
            $api->get('/', 'SalesOrderController@index');
            $api->get('show', 'SalesOrderController@show');
            $api->post('create', 'SalesOrderController@store');
            $api->post('edit', 'SalesOrderController@update');
            $api->post('delete', 'SalesOrderController@destroy');
        });

        $api->group(['prefix' => 'salesteams'], function ($api) {
            $api->get('/', 'SalesTeamController@index');
            $api->get('show', 'SalesTeamController@show');
            $api->post('create', 'SalesTeamController@store');
            $api->post('edit', 'SalesTeamController@update');
            $api->post('delete', 'SalesTeamController@destroy');
        });

        $api->group(['prefix' => 'staff'], function ($api) {
            $api->get('/', 'StaffController@show');
            $api->post('create', 'StaffController@store');
            $api->post('edit', 'StaffController@update');
            $api->post('delete', 'StaffController@destroy');
        });

        $api->group(['prefix' => 'tasks'], function ($api) {
            $api->get('/', 'TaskController@index');
            $api->post('create', 'TaskController@store');
            $api->post('edit', 'TaskController@update');
            $api->post('delete', 'TaskController@destroy');
        });
    });

    //routes for customer
    $api->group(['prefix' => 'customer', 'middleware' => 'api.customer'], function ($api) {
        $api->get('contract', 'CustomerController@contract');
        $api->get('invoice', 'CustomerController@invoice');
        $api->get('quotation', 'CustomerController@quotation');
        $api->get('sales_order', 'CustomerController@salesOrder');
    });
});
*/
