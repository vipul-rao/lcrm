@extends('layouts.user')
{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    @include('flash::message')
    @if($errors->any())
        <div class="alert alert-danger">
            {{  trans('settings.mandatory_fields_valid') }}
        </div>
    @endif
    <div class="card" xmlns:v-bind="http://symfony.com/schema/routing">
        <div class="card-body">
            {!! Form::open(['url' => url('setting'), 'method' => 'post', 'files'=> true]) !!}
            <div class="nav-tabs-custom" id="setting_tabs">
                <ul class="nav nav-tabs settings">
                    <li class="nav-item">
                        <a class="active" href="#general_configuration"
                           data-toggle="tab" title="{{ trans('settings.general_configuration') }}"><i
                                    class="material-icons md-24">build</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#payment_configuration"
                           data-toggle="tab" title="{{ trans('settings.payment_configuration') }}"><i
                                    class="material-icons md-24">attach_money</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#start_number_prefix_configuration"
                           data-toggle="tab" title="{{ trans('settings.start_number_prefix_configuration') }}"><i
                                    class="material-icons md-24">settings_applications</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#paypal_settings"
                           data-toggle="tab" title="{{ trans('settings.paypal_settings') }}"><i
                                    class="material-icons md-24">payment</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#stripe_settings"
                           data-toggle="tab" title="{{ trans('settings.stripe_settings') }}"><i
                                    class="material-icons md-24">vpn_key</i></a>
                    </li>
                    <li class="nav-item">
                        <a href="#europian_tax"
                           data-toggle="tab" title="{{ trans('settings.europian_tax') }}"><i
                                    class="fa fa-money md-24"></i></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="general_configuration">
                        <div class="form-group required {{ $errors->has('site_logo_file') ? 'has-error' : '' }} ">
                            {!! Form::label('site_logo_file', trans('settings.organization_logo'), ['class' => 'control-label required']) !!}
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="row">
                                        <image-upload name="site_logo_file" old-image="{{ url((isset($settings['site_logo'])?$settings['site_logo']:'uploads/site/logo.png')) }}"></image-upload>
                                    </div>
                                </div>
                            </div>
                            <span class="help-block">{{ $errors->first('site_logo_file', ':message') }}</span>

                        </div>
                        <div class="form-group required {{ $errors->has('site_name') ? 'has-error' : '' }}">
                            {!! Form::label('site_name', trans('settings.organization_name'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::text('site_name', old('site_name', (isset($orgSettings['site_name'])?$orgSettings['site_name']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('site_name', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('address1') ? 'has-error' : '' }}">
                            {!! Form::label('site_name', trans('settings.address1'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::text('address1', old('address1', (isset($orgSettings['address1'])?$orgSettings['address1']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('address1', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('address2') ? 'has-error' : '' }}">
                            {!! Form::label('address2', trans('settings.address2'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::text('address2', old('address2', (isset($orgSettings['address2'])?$orgSettings['address2']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('address2', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('site_email') ? 'has-error' : '' }}">
                            {!! Form::label('site_email', trans('settings.organization_email'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::text('site_email', old('site_email', (isset($orgSettings['site_email'])?$orgSettings['site_email']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('site_email', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('phone') ? 'has-error' : '' }}">
                            {!! Form::label('phone', trans('settings.phone'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::text('phone', old('phone', (isset($orgSettings['phone'])?$orgSettings['phone']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('fax') ? 'has-error' : '' }}">
                            {!! Form::label('fax', trans('settings.fax'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::text('fax', old('fax', (isset($orgSettings['fax'])?$orgSettings['fax']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('date_format') ? 'has-error' : '' }}">
                            {!! Form::label('date_format', trans('settings.date_format'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                <div class="radio">
                                    {!! Form::radio('date_format', 'F j,Y',((isset($orgSettings['date_format'])?$orgSettings['date_format']:"")=='F j,Y')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('date_format', date('F j,Y'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'Y-d-m',((isset($orgSettings['date_format'])?$orgSettings['date_format']:"")=='Y-d-m')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('date_format', date('Y-d-m'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'd.m.Y.',((isset($orgSettings['date_format'])?$orgSettings['date_format']:"")=='d.m.Y.')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('date_format', date('d.m.Y.'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'd.m.Y',((isset($orgSettings['date_format'])?$orgSettings['date_format']:"")=='d.m.Y')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('date_format', date('d.m.Y'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'd/m/Y',((isset($orgSettings['date_format'])?$orgSettings['date_format']:"")=='d/m/Y')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('date_format', date('d/m/Y'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'm/d/Y',((isset($orgSettings['date_format'])?$orgSettings['date_format']:"")=='m/d/Y')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('date_format', date('m/d/Y'))  !!}
                                </div>
                                <div class="form-inline">
                                    {!! Form::label('custom_format', trans('settings.custom_format'))  !!}
                                    {!! Form::input('text','date_format_custom', config('settings.date_format'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <span class="help-block">{{ $errors->first('date_format', ':message') }}</span>
                        </div>
                        <a href="{{url('http://php.net/manual/en/function.date.php')}}">{!! trans('settings.date_time_format') !!}</a>
                        <div class="form-group required {{ $errors->has('time_format') ? 'has-error' : '' }}">
                            {!! Form::label('time_format', trans('settings.time_format'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                <div class="radio">
                                    {!! Form::radio('time_format', 'g:i a',((isset($orgSettings['time_format'])?$orgSettings['time_format']:"")=='g:i a')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('time_format', date('g:i a'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('time_format', 'g:i A',((isset($orgSettings['time_format'])?$orgSettings['time_format']:"")=='g:i A')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('time_format', date('g:i A'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('time_format', 'H:i',((isset($orgSettings['time_format'])?$orgSettings['time_format']:"")=='H:i')?true:false,['class' => 'icheck'])  !!}
                                    {!! Form::label('time_format', date('H:i'))  !!}
                                </div>
                                <div class="form-inline">
                                    {!! Form::label('custom_format', trans('settings.custom_format'))  !!}
                                    {!! Form::input('text','time_format_custom', config('settings.time_format'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <span class="help-block">{{ $errors->first('date_format', ':message') }}</span>
                        </div>
                        <div class="form-group required {{ $errors->has('currency') ? 'has-error' : '' }}">
                            {!! Form::label('currency', trans('settings.currency'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::select('currency', $currency, old('currency', (isset($orgSettings['currency'])?$orgSettings['currency']:"")), ['id'=>'currency','class' => 'form-control select2']) !!}
                                <span class="help-block">{{ $errors->first('currency', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('language') ? 'has-error' : '' }}">
                            {!! Form::label('language', trans('Language'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::select('language', $languages, old('language',isset($orgSettings['language'])?$orgSettings['language']:''), ['class' => 'form-control select2']) !!}
                                <span class="help-block">{{ $errors->first('language', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="payment_configuration">
                        <div class="form-group required {{ $errors->has('sales_tax') ? 'has-error' : '' }}">
                            {!! Form::label('sales_tax', trans('settings.sales_tax').'%', ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','sales_tax', old('sales_tax', (isset($orgSettings['sales_tax'])?$orgSettings['sales_tax']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('sales_tax', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('payment_term1') ? 'has-error' : '' }}">
                            {!! Form::label('payment_term1', trans('settings.payment_term1'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','payment_term1', old('payment_term1', (isset($orgSettings['payment_term1'])?$orgSettings['payment_term1']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('payment_term1', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('payment_term2') ? 'has-error' : '' }}">
                            {!! Form::label('payment_term2', trans('settings.payment_term2'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','payment_term2', old('payment_term2', (isset($orgSettings['payment_term2'])?$orgSettings['payment_term2']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('payment_term2', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('payment_term3') ? 'has-error' : '' }}">
                            {!! Form::label('payment_term3', trans('settings.payment_term3'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','payment_term3', old('payment_term3', (isset($orgSettings['payment_term3'])?$orgSettings['payment_term3']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('payment_term3', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('opportunities_reminder_days') ? 'has-error' : '' }}">
                            {!! Form::label('opportunities_reminder_days', trans('settings.opportunities_reminder_days'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','opportunities_reminder_days', old('opportunities_reminder_days', (isset($orgSettings['opportunities_reminder_days'])?$orgSettings['opportunities_reminder_days']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('opportunities_reminder_days', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('invoice_reminder_days') ? 'has-error' : '' }}">
                            {!! Form::label('invoice_reminder_days', trans('settings.invoice_reminder_days'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','invoice_reminder_days', old('invoice_reminder_days', (isset($orgSettings['invoice_reminder_days'])?$orgSettings['invoice_reminder_days']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('invoice_reminder_days', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="start_number_prefix_configuration">
                        <div class="form-group required {{ $errors->has('quotation_prefix') ? 'has-error' : '' }}">
                            {!! Form::label('quotation_prefix', trans('settings.quotation_prefix'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::text('quotation_prefix', old('quotation_prefix', (isset($orgSettings['quotation_prefix'])?$orgSettings['quotation_prefix']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('quotation_prefix', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('quotation_start_number') ? 'has-error' : '' }}">
                            {!! Form::label('quotation_start_number', trans('settings.quotation_start_number'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','quotation_start_number', old('quotation_start_number', (isset($orgSettings['quotation_start_number'])?$orgSettings['quotation_start_number']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('quotation_start_number', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('quotation_template') ? 'has-error' : '' }}">
                            {!! Form::label('quotation_template', trans('settings.quotation_template'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::select('quotation_template', $quotation_template, old('quotation_template', (isset($orgSettings['quotation_template'])?$orgSettings['quotation_template']:"")), ['id'=>'quotation_template','class' => 'form-control select2']) !!}
                                <span class="help-block">{{ $errors->first('quotation_template', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('sales_prefix') ? 'has-error' : '' }}">
                            {!! Form::label('sales_prefix', trans('settings.sales_prefix'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::text('sales_prefix', old('sales_prefix', (isset($orgSettings['sales_prefix'])?$orgSettings['sales_prefix']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('sales_prefix', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('sales_start_number') ? 'has-error' : '' }}">
                            {!! Form::label('sales_start_number', trans('settings.sales_start_number'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','sales_start_number', old('sales_start_number', (isset($orgSettings['sales_start_number'])?$orgSettings['sales_start_number']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('sales_start_number', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('saleorder_template') ? 'has-error' : '' }}">
                            {!! Form::label('saleorder_template', trans('settings.saleorder_template'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::select('saleorder_template', $saleorder_template, old('saleorder_template', (isset($orgSettings['saleorder_template'])?$orgSettings['saleorder_template']:"")), ['id'=>'saleorder_template','class' => 'form-control select2']) !!}
                                <span class="help-block">{{ $errors->first('saleorder_template', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('invoice_prefix') ? 'has-error' : '' }}">
                            {!! Form::label('invoice_prefix', trans('settings.invoice_prefix'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::text('invoice_prefix', old('invoice_prefix', (isset($orgSettings['invoice_prefix'])?$orgSettings['invoice_prefix']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('invoice_prefix', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('invoice_start_number') ? 'has-error' : '' }}">
                            {!! Form::label('invoice_start_number', trans('settings.invoice_start_number'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','invoice_start_number', old('invoice_start_number', (isset($orgSettings['invoice_start_number'])?$orgSettings['invoice_start_number']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('invoice_start_number', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('invoice_template') ? 'has-error' : '' }}">
                            {!! Form::label('invoice_template', trans('settings.invoice_template'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::select('invoice_template', $invoice_template, old('invoice_template', (isset($orgSettings['invoice_template'])?$orgSettings['invoice_template']:"")), ['id'=>'invoice_template','class' => 'form-control select2']) !!}
                                <span class="help-block">{{ $errors->first('invoice_template', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('invoice_payment_prefix') ? 'has-error' : '' }}">
                            {!! Form::label('invoice_payment_prefix', trans('settings.invoice_payment_prefix'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::text('invoice_payment_prefix', old('invoice_payment_prefix', (isset($orgSettings['invoice_payment_prefix'])?$orgSettings['invoice_payment_prefix']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('invoice_payment_prefix', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('invoice_payment_start_number') ? 'has-error' : '' }}">
                            {!! Form::label('invoice_payment_start_number', trans('settings.invoice_payment_start_number'), ['class' => 'control-label required']) !!}
                            <div class="controls">
                                {!! Form::input('number','invoice_payment_start_number', old('invoice_payment_start_number', (isset($orgSettings['invoice_payment_start_number'])?$orgSettings['invoice_payment_start_number']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('invoice_payment_start_number', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="paypal_settings">
                        <div class="form-group required {{ $errors->has('paypal_mode') ? 'has-error' : '' }}">
                            {!! Form::label('paypal_mode', trans('settings.paypal_mode'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                <div class="form-inline">
                                    <div class="radio">
                                        <div class="form-inline">
                                            {!! Form::radio('paypal_mode', 'sandbox',(isset($orgSettings['paypal_mode']) && $orgSettings['paypal_mode']=='sandbox')?true:false,['class' => 'icheck sandbox'])  !!}
                                            {!! Form::label('true', trans('settings.sandbox'),['class'=>'ml-1 mr-2'])  !!}
                                        </div>
                                    </div>
                                    <div class="radio">
                                        <div class="form-inline">
                                            {!! Form::radio('paypal_mode', 'live', (isset($orgSettings['paypal_mode']) && $orgSettings['paypal_mode']=='live')?true:false,['class' => 'icheck live'])  !!}
                                            {!! Form::label('false', trans('settings.live'),['class'=>'ml-1 mr-2']) !!}
                                        </div>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('paypal_mode', ':message') }}</span>
                            </div>
                        </div>
                        <div class="paypal_sandbox">
                            <div class="form-group required {{ $errors->has('paypal_sandbox_username') ? 'has-error' : '' }}">
                                {!! Form::label('paypal_sandbox_username', trans('settings.paypal_sandbox_username'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','paypal_sandbox_username', old('paypal_sandbox_username', (isset($orgSettings['paypal_sandbox_username'])?$orgSettings['paypal_sandbox_username']:"")), ['class' => 'form-control']) !!}
                                    <span class="help-block">{{ $errors->first('paypal_sandbox_username', ':message') }}</span>
                                </div>
                            </div>

                            <div class="form-group required {{ $errors->has('paypal_sandbox_password') ? 'has-error' : '' }}">
                                {!! Form::label('paypal_sandbox_password', trans('settings.paypal_sandbox_password'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','paypal_sandbox_password', old('paypal_sandbox_password', (isset($orgSettings['paypal_sandbox_password'])?$orgSettings['paypal_sandbox_password']:"")), ['class' => 'form-control']) !!}
                                    <span class="help-block">{{ $errors->first('paypal_sandbox_password', ':message') }}</span>
                                </div>
                            </div>

                            <div class="form-group required {{ $errors->has('paypal_sandbox_signature') ? 'has-error' : '' }}">
                                {!! Form::label('paypal_sandbox_signature', trans('settings.paypal_sandbox_signature'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','paypal_sandbox_signature', old('paypal_sandbox_signature', (isset($orgSettings['paypal_sandbox_signature'])?$orgSettings['paypal_sandbox_signature']:"")), ['class' => 'form-control']) !!}
                                    <span class="help-block">{{ $errors->first('paypal_sandbox_signature', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="paypal_live">
                            <div class="form-group required {{ $errors->has('paypal_live_username') ? 'has-error' : '' }}">
                                {!! Form::label('paypal_live_username', trans('settings.paypal_live_username'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','paypal_live_username', old('paypal_live_username', (isset($orgSettings['paypal_live_username'])?$orgSettings['paypal_live_username']:"")), ['class' => 'form-control']) !!}
                                    <span class="help-block">{{ $errors->first('paypal_live_username', ':message') }}</span>
                                </div>
                            </div>

                            <div class="form-group required {{ $errors->has('paypal_live_password') ? 'has-error' : '' }}">
                                {!! Form::label('paypal_live_password', trans('settings.paypal_live_password'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','paypal_live_password', old('paypal_live_password', (isset($orgSettings['paypal_live_password'])?$orgSettings['paypal_live_password']:"")), ['class' => 'form-control']) !!}
                                    <span class="help-block">{{ $errors->first('paypal_live_password', ':message') }}</span>
                                </div>
                            </div>

                            <div class="form-group required {{ $errors->has('paypal_live_signature') ? 'has-error' : '' }}">
                                {!! Form::label('paypal_live_signature', trans('settings.paypal_live_signature'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','paypal_live_signature', old('paypal_live_signature', (isset($orgSettings['paypal_live_signature'])?$orgSettings['paypal_live_signature']:"")), ['class' => 'form-control']) !!}
                                    <span class="help-block">{{ $errors->first('paypal_live_signature', ':message') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="stripe_settings">
                        <div class="form-group required {{ $errors->has('stripe_publishable') ? 'has-error' : '' }}">
                            {!! Form::label('stripe_publishable', trans('settings.stripe_publishable'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::input('text','stripe_publishable', old('stripe_publishable', (isset($orgSettings['stripe_publishable'])?$orgSettings['stripe_publishable']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('stripe_publishable', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('stripe_secret') ? 'has-error' : '' }}">
                            {!! Form::label('stripe_secret', trans('settings.stripe_secret'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::input('text','stripe_secret', old('stripe_secret', (isset($orgSettings['stripe_secret'])?$orgSettings['stripe_secret']:"")), ['class' => 'form-control']) !!}
                                <span class="help-block">{{ $errors->first('stripe_secret', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="europian_tax">
                        <div class="alert alert-danger">
                            {{ trans('settings.vat_applied_to_all_customers') }}
                        </div>
                        @if(isset($settings['europian_tax']) && $settings['europian_tax']=='true')
                            <div class="form-group vat required {{ $errors->has('vat_number') ? 'has-error' : '' }}">
                                {!! Form::label('vat_number', trans('settings.vat_number'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {!! Form::input('text','vat_number', old('vat_number', isset($orgSettings['vat_number'])?$orgSettings['vat_number']:''), ['class' => 'form-control','placeholder'=>'Provide your Vat Number to avoid European Vat while subscribing']) !!}
                                    <span class="help-block">{{ $errors->first('vat_number', ':message') }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="form-group required {{ $errors->has('europian_tax') ? 'has-error' : '' }}">
                            {!! Form::label('email_driver', trans('settings.europian_tax').' For Customers', ['class' => 'control-label']) !!}
                            <div class="controls">
                                <div class="form-inline">
                                    <div class="radio">
                                        <div class="form-inline">
                                            {!! Form::radio('europian_tax', 'true',(isset($orgSettings['europian_tax'])&&$orgSettings['europian_tax']=='true')?true:false,['id'=>'europian_tax_true','class' => 'europian_tax icheck'])  !!}
                                            {!! Form::label('true', 'TRUE',['class'=>'ml-1 mr-2'])  !!}
                                        </div>
                                    </div>
                                    <div class="radio">
                                        <div class="form-inline">
                                            {!! Form::radio('europian_tax', 'false', (isset($orgSettings['europian_tax'])&&$orgSettings['europian_tax']=='false')?true:false,['id'=>'europian_tax_false','class' => 'europian_tax icheck'])  !!}
                                            {!! Form::label('false', 'FALSE',['class'=>'ml-1']) !!}
                                        </div>
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('europian_tax', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Form Actions -->
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success"><i
                                class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                </div>
            </div>
            <!-- ./ form actions -->

            {!! Form::close() !!}
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $(".select2").select2({
                theme:"bootstrap"
            });
//            $("#language").select2({
//                theme:'bootstrap'
//            });
            $("input[name='date_format']").on('ifChecked', function () {
                if ("date_format_custom_radio" != $(this).attr("id"))
                    $("input[name='date_format_custom']").val($(this).val()).siblings('.example').text($(this).siblings('span').text());
            });
            $("input[name='date_format_custom']").focus(function () {
                $("#date_format_custom_radio").attr("checked", "checked");
            });

            $("input[name='time_format']").on('ifChecked', function () {
                if ("time_format_custom_radio" != $(this).attr("id"))
                    $("input[name='time_format_custom']").val($(this).val()).siblings('.example').text($(this).siblings('span').text());
            });
            $("input[name='time_format_custom']").focus(function () {
                $("#time_format_custom_radio").attr("checked", "checked");
            });
            $("input[name='date_format_custom'], input[name='time_format_custom']").on('ifChecked', function () {
                var format = $(this);
                format.siblings('img').css('visibility', 'visible');
                $.post(ajaxurl, {
                    action: 'date_format_custom' == format.attr('name') ? 'date_format' : 'time_format',
                    date: format.val()
                }, function (d) {
                    format.siblings('img').css('visibility', 'hidden');
                    format.siblings('.example').text(d);
                });
            });
            $(".paypal_live,.paypal_sandbox").hide();
            if('{{ isset($orgSettings['paypal_mode']) && !empty($orgSettings['paypal_mode']) }}'){
                if('{{ isset($orgSettings['paypal_mode']) && $orgSettings['paypal_mode']=='sandbox' }}'){
                    $(".paypal_sandbox").show();
                }else{
                    $(".paypal_live").show();
                }
            }
            if('{{ old('paypal_mode')=='sandbox' }}'){
                $(".paypal_sandbox").show();
                $(".paypal_live").hide();
            }
            if('{{ old('paypal_mode')=='live' }}'){
                $(".paypal_sandbox").hide();
                $(".paypal_live").show();
            }
            $(".sandbox").on("ifChecked",function(){
                $(".paypal_sandbox").show();
                $(".paypal_live").hide();
            });
            $(".sandbox").on("ifUnchecked",function(){
                $(".paypal_sandbox").hide();
                $(".paypal_live").show();
            });
        });
    </script>
@stop
