<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::pattern('slug', '[a-z0-9-]+');
Route::pattern('slug2', '[a-z_]+');
Route::pattern('slug3', '[a-z0-9]+');
Route::pattern('id', '[0-9]+');

/*
|--------------------------------------------------------------------------
| include Installer routes
|--------------------------------------------------------------------------
|
*/
require 'install.php';

/*
|--------------------------------------------------------------------------
| Stripe webhook
|--------------------------------------------------------------------------
|
*/
Route::post(
    'stripe/webhook',
    '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook'
);
Route::post('ipn/notify','Users\PaypalSubscriptionController@postNotify');

/******************   APP routes  ********************************/
Route::get('home', 'AuthController@getSignin');
Route::get('invite/{slug3}', 'AuthController@getSignup');
Route::post('invite/{slug3}', 'AuthController@postSignup');
//route after user login into system
Route::get('signin', 'AuthController@getSignin');
Route::post('signin', 'AuthController@postSignin');
Route::get('forgot', 'AuthController@getForgotPassword')->name('forgot-password');
Route::post('password', 'AuthController@postForgotPassword');
Route::get('reset_password/{token}', 'AuthController@getForgotPasswordConfirm');
Route::post('reset_password/{token}', 'AuthController@postForgotPasswordConfirm')->name('password.reset');
Route::get('logout', 'AuthController@getLogout');
// Subscription Expired
Route::get('subscription-expired', 'Users\PaymentController@subscriptionExpired');

//===============Frontend ================

Route::get('/', 'IndexController@index');
Route::get('about_us','IndexController@aboutUs');
Route::get('contactus', 'IndexController@contactUs');
Route::post('contactus', 'IndexController@storeContactUs');
Route::get('privacy', 'IndexController@privacy');
Route::get('pricing', 'IndexController@pricing');

//register
Route::get('register', 'RegisterController@create');
Route::post('register', 'RegisterController@store');
//===============Frontend ================

/*
|--------------------------------------------------------------------------
| include customer routes
|--------------------------------------------------------------------------
|
*/
require 'web_customer.php';

Route::group(['middleware' => ['sentinel', 'xss_protection']], function () {
    Route::get('profile', 'AuthController@getProfile');
    Route::get('account', 'AuthController@getAccount');
    Route::put('account/{user}', 'AuthController@postAccount');
});
Route::group(['middleware' => ['sentinel', 'admin', 'userdata', 'xss_protection'], 'namespace' => 'Admin'], function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::get('', 'DashboardController@index');

        Route::get('setting', 'SettingsController@index');
        Route::post('setting', 'SettingsController@update');

        Route::get('support', 'SupportController@index');
        Route::get('support/all', 'SupportController@getData');
        Route::get('support/{id}/get', 'SupportController@getMail');
        Route::put('support/{id}/solve', 'SupportController@markAsSolved');
        Route::get('support/{id}/getSent', 'SupportController@getSentMail');
        Route::get('support/mail-template/{id}', 'SupportController@getMailTemplate');
        Route::post('support/{id}/comment', 'SupportController@postComment');
        Route::get('support/data', 'SupportController@getAllData');
        Route::get('support/tickets', 'SupportController@getTickets');
        Route::get('support/closed-tickets', 'SupportController@getClosedTickets');
        Route::post('support/send', 'SupportController@sendEmail');
        Route::get('support/sent', 'SupportController@getSent');
        Route::post('support/mark-as-solved', 'SupportController@postMarkAsSolved');
        Route::post('support/delete', 'SupportController@postDelete');
    });

    Route::group(['prefix' => 'organizations'], function () {
        Route::get('data', 'OrganizationController@data');
        Route::get('{organization}/delete', 'OrganizationController@delete');
        Route::get('{organization}/activate', 'OrganizationController@activate');
        Route::get('activity/payment/{transaction}', 'OrganizationController@paymentActivity');
    });
    Route::resource('organizations', 'OrganizationController');

    Route::group(['prefix' => 'admin/payplan'], function () {
        Route::get('data', 'PayPlanController@data');
        Route::get('{payplan}/show', 'PayPlanController@show');
    });
    Route::resource('admin/payplan', 'PayPlanController');

    Route::group(['prefix' => 'admin/option'], function () {
        Route::get('data/{slug2}', 'OptionController@data');
        Route::get('data', 'OptionController@data');
        Route::get('{option}/show', 'OptionController@show');
        Route::get('{option}/delete', 'OptionController@delete');
    });
    Route::resource('admin/option', 'OptionController');

    Route::group(['prefix' => 'admin/subscription'], function () {
        Route::get('data', 'SubscriptionController@data');
        Route::get('{subscription}/show', 'SubscriptionController@show');
        Route::get('{subscription}/active', 'SubscriptionController@activeSubscription');
        Route::get('{subscription}/extend', 'SubscriptionController@extendSubscription');

        Route::post('{subscription}/cancel', 'SubscriptionController@cancelSubscription');
        Route::post('{subscription}/resume', 'SubscriptionController@resumeSubscription');
        Route::post('{subscription}/extend', 'SubscriptionController@postExtendSubscription');
        Route::post('{subscription}/change_plan/{id}', 'SubscriptionController@changePlan');
        Route::get('{subscription}/change', 'SubscriptionController@changeSubscription');

        Route::get('trialing', 'SubscriptionController@trialing');
        Route::get('trialing/data', 'SubscriptionController@trialingData');
        Route::get('expired_subscription', 'SubscriptionController@expiredSubscription');
        Route::get('expired_subscription/data', 'SubscriptionController@expiredSubscriptionData');
        Route::get('expired_trial', 'SubscriptionController@expiredTrial');
        Route::get('expired_trial/data', 'SubscriptionController@expiredTrialData');

        Route::post('{subscription}/suspend', 'SubscriptionController@suspendPaypalSubscription');
    });
    Route::resource('admin/subscription', 'SubscriptionController');

    Route::group(['prefix' => 'admin/payment'], function () {
        Route::get('data', 'PaymentController@data');
        Route::get('{payment}/show', 'PaymentController@show');
        Route::get('{payment}/edit', 'PaymentController@edit');
    });
    Route::resource('admin/payment', 'PaymentController');

    Route::group(['prefix' => 'admin/email_template'], function () {
        Route::get('data', 'EmailTemplateController@data');
        Route::get('{email_template}/show', 'EmailTemplateController@show');
        Route::get('{email_template}/delete', 'EmailTemplateController@delete');
    });
    Route::resource('admin/email_template', 'EmailTemplateController');

    Route::group(['prefix' => 'admin/contactus'], function () {
        Route::get('data', 'ContactUsController@data');
        Route::get('{payplan}/show', 'ContactUsController@show');
        Route::get('{payplan}/delete', 'ContactUsController@delete');
    });
    Route::resource('admin/contactus', 'ContactUsController');

    Route::group(['prefix' => 'admin/backup'], function () {
        Route::get('/','BackupController@index');
        Route::get('store','BackupController@store');
        Route::get('clean','BackupController@clean');
    });
});

Route::group(['middleware' => ['sentinel', 'userdata', 'subscription', 'xss_protection'], 'namespace' => 'Users'], function () {
    Route::get('dashboard', 'DashboardController@dashboard');

    Route::get('setting', 'SettingsController@index');
    Route::post('setting', 'SettingsController@update');

    Route::get('support', 'SupportController@index');
    Route::get('support/all', 'SupportController@getData');
    Route::get('support/mail-template/{id}', 'SupportController@getMailTemplate');
    Route::get('support/{id}/get', 'SupportController@getTicket');
    Route::put('support/{id}/solve', 'SupportController@markAsSolved');
    Route::get('support/{id}/getSent', 'SupportController@getSentMail');
    Route::post('support/{id}/comment', 'SupportController@postComment');
    Route::get('support/data', 'SupportController@getAllData');
    Route::get('support/tickets', 'SupportController@getTickets');
    Route::get('support/closed-tickets', 'SupportController@getClosedTickets');
    Route::post('support/create', 'SupportController@createTicket');
    Route::get('support/sent', 'SupportController@getSent');
    Route::post('support/mark-as-solved', 'SupportController@postMarkAsSolved');
    Route::post('support/delete', 'SupportController@postDelete');

    Route::group(['prefix' => 'payment'], function () {
        Route::get('', 'PaymentController@pay');
        Route::post('stripe', 'PaymentController@stripe');
        Route::get('success', 'PaymentController@success');
        Route::get('status', 'PaymentController@status');
        Route::get('{id}/cancel', 'PaymentController@cancel');
        Route::get('{id}/stripe', 'PaymentController@stripeWithoutCard');
    });
    Route::post('change_generic_plan/stripe', 'PaymentController@stripeWithoutTrial');

    Route::group(['prefix' => 'subscription'], function () {
        Route::get('/', 'SubscriptionController@index');
        Route::get('data', 'SubscriptionController@data');
        Route::post('{subscription}/cancel', 'SubscriptionController@cancelSubscription');
        Route::post('{subscription}/resume', 'SubscriptionController@resumeSubscription');
        Route::post('change_plan/{id}', 'SubscriptionController@changePlan');
        Route::get('change', 'SubscriptionController@changeSubscription');
        Route::get('change_generic_plan', 'SubscriptionController@changeGenericPlan');
        Route::post('{subscription}/suspend', 'SubscriptionController@suspendPaypalSubscription');
        Route::post('change_plan_paypal/{id}', 'PaypalSubscriptionController@changePlanPaypal');
        Route::get('change_plan_paypal/{id}', 'PaypalSubscriptionController@changePlanPaypal');
    });
    Route::get('update_card', 'SubscriptionController@updateCardIndex');
    Route::post('update_card', 'SubscriptionController@updateCard');

    Route::group(['prefix' => 'payment'], function () {
        Route::post('{id}/paypal', 'PaypalSubscriptionController@paypal');
        Route::get('{id}/paypal', 'PaypalSubscriptionController@paypal');
        Route::get('{id}/paypal_success', 'PaypalSubscriptionController@paypalSuccess');
        Route::get('{id}/paypal_cancel', 'PaypalSubscriptionController@paypalCancelUrl');
        Route::get('{id}/paypal_without_card', 'PaypalSubscriptionController@paypalWithoutCard');
    });
    Route::get('paypal_transactions','PaypalSubscriptionController@paypalTransactionInOrganization');
    Route::get('activity/payment/{transaction}','PaypalSubscriptionController@paymentActivity');

    Route::group(['prefix' => 'change_generic_plan'], function () {
        Route::post('{id}/paypal', 'PaypalSubscriptionController@paypalWithoutTrial');
        Route::get('{id}/paypal_success', 'PaypalSubscriptionController@paypalSuccessWithoutTrial');
        Route::get('{id}/paypal_cancel', 'PaypalSubscriptionController@paypalCancelUrlWithoutTrial');
    });
    Route::post('resume_paypal_subscription/resume', 'PaypalSubscriptionController@resumePaypalSubscription');

    Route::get('change_subscription/{id}/paypal_success','PaypalSubscriptionController@paypalChangeSubscriptionSuccess');

    Route::group(['prefix' => 'salesteam'], function () {
        Route::get('data', 'SalesteamController@data');
        Route::get('import', 'SalesteamController@getImport');
        Route::post('import', 'SalesteamController@postImport');
        Route::post('ajax-store', 'SalesteamController@postAjaxStore');
        Route::get('download-template', 'SalesteamController@downloadExcelTemplate');
        Route::get('{salesteam}/delete', 'SalesteamController@delete');
        Route::get('{salesteam}/show', 'SalesteamController@show');
    });
    Route::resource('salesteam', 'SalesteamController');

    Route::group(['prefix' => 'category'], function () {
        Route::get('data', 'CategoryController@data');
        Route::get('{category}/delete', 'CategoryController@delete');

        Route::get('import', 'CategoryController@getImport');
        Route::post('import', 'CategoryController@postImport');
        Route::post('ajax-store', 'CategoryController@postAjaxStore');
        Route::get('download-template', 'CategoryController@downloadExcelTemplate');
    });
    Route::resource('category', 'CategoryController');

    Route::group(['prefix' => 'lead'], function () {
        Route::get('data', 'LeadController@data');
        Route::get('ajax_state_list', 'LeadController@ajaxStateList');
        Route::get('ajax_city_list', 'LeadController@ajaxCityList');
        Route::get('{lead}/delete', 'LeadController@delete');
        Route::get('{lead}/show', 'LeadController@show');
        //Excel Import
        Route::get('import', 'LeadController@getImport');
        Route::post('import', 'LeadController@postImport');
        Route::post('ajax-store', 'LeadController@postAjaxStore');
        Route::get('download-template', 'LeadController@downloadExcelTemplate');
    });
    Route::resource('lead', 'LeadController');

    Route::group(['prefix' => 'opportunity'], function () {
        Route::get('data', 'OpportunityController@data');
        Route::get('{opportunity}/delete', 'OpportunityController@delete');
        Route::get('{opportunity}/show', 'OpportunityController@show');
        Route::get('{opportunity}/lost', 'OpportunityController@lost');
        Route::post('{opportunity}/update_lost', 'OpportunityController@updateLost');
        Route::get('{opportunity}/won', 'OpportunityController@won');
        Route::get('{opportunity}/convert_to_quotation', 'OpportunityController@convertToQuotation');
        Route::post('{opportunity}/opportunity_archive', 'OpportunityController@convertToArchive');
        Route::get('ajax_agent_list', 'OpportunityController@ajaxAgentList');
        Route::get('ajax_main_staff_list', 'OpportunityController@ajaxMainStaffList');
    });
    Route::resource('opportunity', 'OpportunityController');

    Route::get('convertedlist_view/{id}/show', 'OpportunityConvertedListController@quatationList');
    Route::group(['prefix' => 'opportunity_archive'], function () {
        Route::get('{opportunity_archive}/show', 'OpportunityArchiveController@show');
        Route::get('data', 'OpportunityArchiveController@data');
    });
    Route::resource('opportunity_archive', 'OpportunityArchiveController');

    Route::group(['prefix' => 'opportunity_delete_list'], function () {
        Route::get('data', 'OpportunityDeleteListController@data');
        Route::get('{opportunity_delete_list}/show', 'OpportunityDeleteListController@show');
        Route::get('{opportunity_delete_list}/restore', 'OpportunityDeleteListController@delete');
        Route::delete('{opportunity_delete_list}', 'OpportunityDeleteListController@restoreOpportunity');
        Route::get('/', 'OpportunityDeleteListController@index');
    });

    Route::group(['prefix' => 'opportunity_converted_list'], function () {
        Route::get('data', 'OpportunityConvertedListController@data');
    });
    Route::resource('opportunity_converted_list', 'OpportunityConvertedListController');

    Route::group(['prefix' => 'company'], function () {
        Route::get('data', 'CompanyController@data');
        Route::get('{company}/show', 'CompanyController@show');
        Route::get('{company}/delete', 'CompanyController@delete');

        Route::get('import', 'CompanyController@getImport');
        Route::post('import', 'CompanyController@postImport');
        Route::post('ajax-store', 'CompanyController@postAjaxStore');
        Route::get('download-template', 'CompanyController@downloadExcelTemplate');
    });
    Route::resource('company', 'CompanyController');

    Route::group(['prefix' => 'customer'], function () {
        Route::get('data', 'CustomerController@data');
        Route::put('{user}/ajax', 'CustomerController@ajaxUpdate');
        Route::get('{customer}/show', 'CustomerController@show');
        Route::get('{customer}/delete', 'CustomerController@delete');

        //Excel Import
        Route::get('import', 'CustomerController@getImport');
        Route::post('import', 'CustomerController@postImport');
        Route::post('ajax-store', 'CustomerController@postAjaxStore');

        Route::post('import-excel-data', 'CustomerController@importExcelData');
        Route::get('download-template', 'CustomerController@downloadExcelTemplate');
    });
    Route::resource('customer', 'CustomerController');

    Route::group(['prefix' => 'call'], function () {
        Route::get('data', 'CallController@data');
        Route::get('{call}/edit', 'CallController@edit');
        Route::get('{call}/delete', 'CallController@delete');
        Route::delete('{call}', 'CallController@destroy');
        Route::put('{call}', 'CallController@update');
        Route::post('', 'CallController@store');
        Route::get('', 'CallController@index');
    });
    Route::resource('call', 'CallController');

    Route::group(['prefix' => 'leadcall'], function () {
        Route::get('{lead}', 'LeadCallController@index');
        Route::get('{lead}/data', 'LeadCallController@data');
        Route::get('{lead}/create', 'LeadCallController@create');
        Route::post('{lead}', 'LeadCallController@store');
        Route::get('{lead}/{call}/edit', 'LeadCallController@edit');
        Route::put('{lead}/{call}', 'LeadCallController@update');
        Route::get('{lead}/{call}/delete', 'LeadCallController@delete');
        Route::delete('{lead}/{call}', 'LeadCallController@destroy');
    });
    Route::resource('leadcall', 'LeadCallController');

    Route::group(['prefix' => 'opportunitycall'], function () {
        Route::get('{opportunity}', 'OpportunityCallController@index');
        Route::get('{opportunity}/data', 'OpportunityCallController@data');
        Route::get('{opportunity}/create', 'OpportunityCallController@create');
        Route::post('{opportunity}', 'OpportunityCallController@store');
        Route::get('{opportunity}/{call}/edit', 'OpportunityCallController@edit');
        Route::put('{opportunity}/{call}', 'OpportunityCallController@update');
        Route::get('{opportunity}/{call}/delete', 'OpportunityCallController@delete');
        Route::delete('{opportunity}/{call}', 'OpportunityCallController@destroy');
    });
    Route::resource('opportunitycall', 'OpportunityCallController');

    Route::group(['prefix' => 'opportunitymeeting'], function () {
        Route::get('{opportunity}', 'OpportunityMeetingController@index');
        Route::get('{opportunity}/data', 'OpportunityMeetingController@data');
        Route::get('{opportunity}/create', 'OpportunityMeetingController@create');
        Route::post('{opportunity}', 'OpportunityMeetingController@store');
        Route::get('{opportunity}/calendar', 'OpportunityMeetingController@calendar');
        Route::post('{opportunity}/calendarData', 'OpportunityMeetingController@calendar_data');
        Route::get('{opportunity}/{meeting}/edit', 'OpportunityMeetingController@edit');
        Route::put('{opportunity}/{meeting}', 'OpportunityMeetingController@update');
        Route::get('{opportunity}/{meeting}/delete', 'OpportunityMeetingController@delete');
        Route::delete('{opportunity}/{meeting}', 'OpportunityMeetingController@destroy');
    });
    Route::resource('opportunitymeeting', 'OpportunityMeetingController');

    Route::group(['prefix' => 'meeting'], function () {
        Route::get('calendar', 'MeetingController@calendar');
        Route::post('calendarData', 'MeetingController@calendar_data');
        Route::get('data', 'MeetingController@data');
        Route::get('{meeting}/delete', 'MeetingController@delete');
    });
    Route::resource('meeting', 'MeetingController');

    Route::group(['prefix' => 'product'], function () {
        Route::get('data', 'ProductController@data');
        Route::get('import', 'ProductController@getImport');
        Route::post('import', 'ProductController@postImport');
        Route::post('ajax-store', 'ProductController@postAjaxStore');
        Route::get('download-template', 'ProductController@downloadExcelTemplate');
        Route::get('{product}/delete', 'ProductController@delete');
        Route::get('{product}/show', 'ProductController@show');
    });
    Route::resource('product', 'ProductController');

    Route::group(['prefix' => 'staff'], function () {
        Route::get('data', 'StaffController@data');
        Route::get('{staff}/show', 'StaffController@show');
        Route::get('{staff}/delete', 'StaffController@delete');
        Route::get('invite', 'StaffController@invite');
        Route::post('invite', 'StaffController@inviteSave');
        Route::get('invite/{id}/cancel', 'StaffController@inviteCancel');
        Route::post('invite/{id}/cancel-invite', 'StaffController@inviteCancelConfirm');
    });
    Route::resource('staff', 'StaffController');

    Route::group(['prefix' => 'qtemplate'], function () {
        Route::get('data', 'QtemplateController@data');
        Route::get('{qtemplate}/delete', 'QtemplateController@delete');
    });
    Route::resource('qtemplate', 'QtemplateController');

    Route::group(['prefix' => 'quotation'], function () {
        Route::get('data', 'QuotationController@data');
        Route::post('send_quotation', 'QuotationController@sendQuotation');
        Route::get('{quotation}/show', 'QuotationController@show');
        Route::get('{quotation}/edit', 'QuotationController@edit');
        Route::get('{quotation}/delete', 'QuotationController@delete');
        Route::get('{quotation}/ajax_create_pdf', 'QuotationController@ajaxCreatePdf');
        Route::get('{quotation}/print_quot', 'QuotationController@printQuot');
        Route::get('{quotation}/make_invoice', 'QuotationController@makeInvoice');
        Route::get('{quotation}/confirm_sales_order', 'QuotationController@confirmSalesOrder');
        Route::put('{quotation}', 'QuotationController@update');
        Route::delete('{quotation}', 'QuotationController@destroy');
        Route::get('ajax_qtemplates_products/{qtemplate}', 'QuotationController@ajaxQtemplatesProducts');
        Route::get('ajax_sales_team_list', 'QuotationController@ajaxSalesTeamList');

        Route::get('draft_quotations_list/data', 'QuotationController@draftQuotations');
        Route::get('draft_quotations', 'QuotationController@draftIndex');
    });
    Route::resource('quotation', 'QuotationController');

    Route::group(['prefix' => 'quotation_converted_list'], function () {
        Route::get('data', 'QuotationConvertedListController@data');
    });
    Route::resource('quotation_converted_list', 'QuotationConvertedListController');
    Route::get('quotation_converted_list/{id}/show', 'QuotationConvertedListController@salesOrderList');

    Route::group(['prefix' => 'quotation_invoice_list'], function () {
        Route::get('data', 'QuotationInvoiceListController@data');
    });
    Route::resource('quotation_invoice_list', 'QuotationInvoiceListController');
    Route::get('quotation_invoice_list/{id}/show', 'QuotationInvoiceListController@invoiceList');

    Route::group(['prefix' => 'quotation_delete_list'], function () {
        Route::get('data', 'QuotationDeleteListController@data');
        Route::get('{quotation_delete_list}/show', 'QuotationDeleteListController@show');
        Route::get('{quotation_delete_list}/restore', 'QuotationDeleteListController@delete');
        Route::delete('{quotation_delete_list}', 'QuotationDeleteListController@restoreQuotation');
        Route::get('/', 'QuotationDeleteListController@index');
    });

    Route::post('calendar/events', 'CalendarController@events');
    Route::resource('calendar', 'CalendarController');

    Route::group(['prefix' => 'sales_order'], function () {
        Route::get('data', 'SalesorderController@data');
        Route::post('send_saleorder', 'SalesorderController@sendSaleorder');
        Route::get('{saleorder}/show', 'SalesorderController@show');
        Route::get('{saleorder}/edit', 'SalesorderController@edit');
        Route::get('{saleorder}/delete', 'SalesorderController@delete');
        Route::get('{saleorder}/ajax_create_pdf', 'SalesorderController@ajaxCreatePdf');
        Route::get('{saleorder}/print_quot', 'SalesorderController@printQuot');
        Route::get('{saleorder}/make_invoice', 'SalesorderController@makeInvoice');
        Route::get('{saleorder}/confirm_sales_order', 'SalesorderController@confirmSalesOrder');
        Route::put('{saleorder}', 'SalesorderController@update');
        Route::delete('{saleorder}', 'SalesorderController@destroy');
        Route::get('ajax_qtemplates_products/{qtemplate}', 'SalesorderController@ajaxQtemplatesProducts');
        Route::get('draft_salesorder_list/data', 'SalesorderController@draftSalesOrders');
        Route::get('draft_salesorders', 'SalesorderController@draftIndex');
    });
    Route::resource('sales_order', 'SalesorderController');

    Route::group(['prefix' => 'salesorder_delete_list'], function () {
        Route::get('data', 'SalesorderDeleteListController@data');
        Route::get('{salesorder_delete_list}/show', 'SalesorderDeleteListController@show');
        Route::get('{salesorder_delete_list}/restore', 'SalesorderDeleteListController@delete');
        Route::delete('{salesorder_delete_list}', 'SalesorderDeleteListController@restoreSalesorder');
        Route::get('/', 'SalesorderDeleteListController@index');
    });

    Route::group(['prefix' => 'salesorder_invoice_list'], function () {
        Route::get('data', 'SalesorderInvoiceListController@data');
    });
    Route::resource('salesorder_invoice_list', 'SalesorderInvoiceListController');
    Route::get('salesorder_invoice_list/{id}/show', 'SalesorderInvoiceListController@invoiceList');

    Route::group(['prefix' => 'invoices_payment_log'], function () {
        Route::get('data', 'InvoicesPaymentController@data');
        Route::get('{invoiceReceivePayment}/show', 'InvoicesPaymentController@show');
        Route::get('{invoiceReceivePayment}/delete', 'InvoicesPaymentController@delete');
        Route::get('payment_logs', 'InvoicesPaymentController@paymentLog');
    });
    Route::resource('invoices_payment_log', 'InvoicesPaymentController');

    Route::get('mailbox', 'MailboxController@index');
    Route::get('mailbox/all', 'MailboxController@getData');
    Route::get('mailbox/mail-template/{id}', 'MailboxController@getMailTemplate');
    Route::get('mailbox/{email}/get', 'MailboxController@getMail');
    Route::get('mailbox/{id}/getSent', 'MailboxController@getSentMail');

    Route::post('mailbox/{id}/reply', 'MailboxController@postReply');
    Route::get('mailbox/data', 'MailboxController@getAllData');
    Route::get('mailbox/received', 'MailboxController@getReceived');
    Route::post('mailbox/send', 'MailboxController@sendEmail');
    Route::get('mailbox/sent', 'MailboxController@getSent');
    Route::post('mailbox/mark-as-read', 'MailboxController@postMarkAsRead');
    Route::post('mailbox/delete', 'MailboxController@postDelete');

    Route::group(['prefix' => 'invoice'], function () {
        Route::get('data', 'InvoiceController@data');
        Route::post('send_invoice', 'InvoiceController@sendInvoice');
        Route::get('{invoice}/show', 'InvoiceController@show');
        Route::get('{invoice}/edit', 'InvoiceController@edit');
        Route::get('{invoice}/delete', 'InvoiceController@delete');
        Route::post('{user}/ajax_customer_details', 'InvoiceController@ajaxCustomerDetails');
        Route::get('{invoice}/ajax_create_pdf', 'InvoiceController@ajaxCreatePdf');
        Route::get('{invoice}/print_quot', 'InvoiceController@printQuot');
    });
    Route::resource('invoice', 'InvoiceController');

    Route::group(['prefix' => 'invoice_delete_list'], function () {
        Route::get('data', 'InvoiceDeleteListController@data');
        Route::get('{invoice_delete_list}/show', 'InvoiceDeleteListController@show');
        Route::get('{invoice_delete_list}/restore', 'InvoiceDeleteListController@delete');
        Route::delete('{invoice_delete_list}', 'InvoiceDeleteListController@restoreInvoice');
        Route::get('/', 'InvoiceDeleteListController@index');
    });

    Route::group(['prefix' => 'paid_invoice'], function () {
        Route::get('data', 'InvoicePaidListController@data');
    });
    Route::resource('paid_invoice', 'InvoicePaidListController');

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('all', 'NotificationController@getAllData');
        Route::post('read', 'NotificationController@postRead');
    });

    Route::get('task', 'TaskController@index');
    Route::post('task/create', 'TaskController@store');
    Route::get('task/data', 'TaskController@data');
    Route::post('task/{task}/edit', 'TaskController@update');
    Route::post('task/{task}/delete', 'TaskController@delete');
});

Route::group(['middleware' => ['sentinel', 'userdata', 'subscription'], 'namespace' => 'Users'], function () {
    Route::group(['prefix' => 'email_template'], function () {
        Route::get('data', 'EmailTemplateController@data');
        Route::get('{email_template}/show', 'EmailTemplateController@show');
        Route::get('{email_template}/delete', 'EmailTemplateController@delete');
    });
    Route::resource('email_template', 'EmailTemplateController');
});
