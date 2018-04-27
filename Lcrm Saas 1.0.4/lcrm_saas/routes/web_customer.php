<?php

Route::group(['middleware' => 'sentinel'], function () {
    //customer routes
    Route::group(['middleware' => ['customer', 'userdata', 'xss_protection', 'subscription'], 'prefix' => 'customers', 'namespace' => 'Customer'], function () {
        Route::get('/', 'DashboardController@index');

        Route::get('setting', 'SettingsController@index');
        Route::post('setting', 'SettingsController@update');

        Route::get('mailbox', 'MailboxController@index');
        Route::get('mailbox/all', 'MailboxController@getData');
        Route::get('mailbox/{id}/get', 'MailboxController@getMail');
        Route::get('mailbox/{id}/getSent', 'MailboxController@getSentMail');
        Route::post('mailbox/{id}/reply', 'MailboxController@postReply');
        Route::get('mailbox/data', 'MailboxController@getAllData');
        Route::get('mailbox/received', 'MailboxController@getReceived');
        Route::post('mailbox/send', 'MailboxController@sendEmail');
        Route::get('mailbox/sent', 'MailboxController@getSent');
        Route::post('mailbox/mark-as-read', 'MailboxController@postMarkAsRead');
        Route::post('mailbox/delete', 'MailboxController@postDelete');

        Route::group(['prefix' => 'quotation'], function () {
            Route::get('data', 'QuotationController@data');
            Route::get('{quotation}/show', 'QuotationController@show');
            Route::get('{quotation}/ajax_create_pdf', 'QuotationController@ajaxCreatePdf');
            Route::get('{quotation}/print_quot', 'QuotationController@printQuot');
            Route::get('{quotation}/accept_quotation', 'QuotationController@acceptQuotation');
            Route::get('{quotation}/reject_quotation', 'QuotationController@rejectQuotation');
        });
        Route::resource('quotation', 'QuotationController');

        Route::group(['prefix' => 'invoice'], function () {
            Route::get('data', 'InvoiceController@data');
            Route::get('{invoice}/show', 'InvoiceController@show');
            Route::get('{invoice}/ajax_create_pdf', 'InvoiceController@ajaxCreatePdf');
            Route::get('{invoice}/print_quot', 'InvoiceController@printQuot');
        });
        Route::resource('invoice', 'InvoiceController');

        Route::group(['prefix' => 'sales_order'], function () {
            Route::get('data', 'SalesorderController@data');
            Route::get('{saleorder}/show', 'SalesorderController@show');
            Route::get('{saleorder}/ajax_create_pdf', 'SalesorderController@ajaxCreatePdf');
            Route::get('{saleorder}/print_quot', 'SalesorderController@printQuot');
        });
        Route::resource('sales_order', 'SalesorderController');

        Route::group(['prefix' => 'payment'], function () {
            Route::get('{invoice}/pay', 'PaymentController@pay');
            Route::post('{invoice}/paypal', 'PaymentController@paypal');
            Route::get('{invoice}/paypal_success', 'PaymentController@paypalSuccess');
            Route::get('{invoice}/paypal_cancel', 'PaymentController@cancel');
            Route::post('{invoice}/stripe', 'PaymentController@stripe');
            Route::get('success', 'PaymentController@success');
            Route::get('cancel', 'PaymentController@cancel');
        });

        Route::group(['prefix' => 'invoices_payment_log'], function () {
            Route::get('data', 'InvoicesPaymentController@data');
            Route::get('{invoiceReceivePayment}/show', 'InvoicesPaymentController@show');
        });
        Route::resource('invoices_payment_log', 'InvoicesPaymentController');
    });
});
