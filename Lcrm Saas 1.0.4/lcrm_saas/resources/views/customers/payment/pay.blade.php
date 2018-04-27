@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}

@section('content')
    <div class="page-header clearfix">
    </div>
    <div class="card">
        <div class="card-body">
            {!! Form::open(array('url' => url('customers/payment/'.$invoice->id.'/paypal'), 'method' => 'post', 'class' => 'form-horizontal')) !!}
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                {!! Form::label('title', trans('invoice.invoice_number'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {{$invoice->invoice_number}}
                                </div>
                                {!! Form::hidden('invoice_number', $invoice->invoice_number) !!}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('title', trans('invoice.unpaid_amount'), ['class' => 'control-label']) !!}
                                <div class="controls">
                                    {{$organizationSettings['currency'].' '.$invoice->unpaid_amount}}
                                </div>
                                {!! Form::hidden('unpaid_amount', $invoice->unpaid_amount) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                @if(($organizationSettings['paypal_sandbox_username']!="" && $organizationSettings['paypal_sandbox_password']!="")
                || ($organizationSettings['paypal_live_username']!="" && $organizationSettings['paypal_live_password']!=""))
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa fa-check-square-o"></i> {{trans('payment.pay_paypal')}}
                    </button>
                @endif
                <br>
            </div>
            {!! Form::close() !!}
            @if($stripe_secret!="" && $stripe_publishable!="")
            {!! Form::open(['url' => url('customers/payment/'.$invoice->id.'/stripe'), 'method' => 'post','id' =>'payment_form']) !!}
            <script
                    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                    data-key="{!!$stripe_publishable !!}"
                    data-amount="{!! $amount*100 !!}"
                    data-image="{{asset($settings['site_logo'])}}"
                    data-name="{!! $invoice->invoice_number !!}"
                    data-currency="{!! $currency !!}"
                    data-locale="auto">
            </script>
            {!! Form::close() !!}
                @endif
        </div>
    </div>
@stop
@section('scripts')

@stop