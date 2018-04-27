@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <a href="#" class="btn btn-primary m-b-10" data-toggle="modal" data-target="#modal-send_by_email"
           onclick="create_pdf({{ $quotation->id}})"><i class="fa fa-envelope-o"></i> {{trans('quotation.send_email')}}
        </a>
        <a href="{{url('quotation/'.$quotation->id.'/print_quot')}}" class="btn btn-primary m-b-10">
            <i class="fa fa-print"></i> {{trans('quotation.print')}}
        </a>
        @if(strtotime(date("m/d/Y"))>strtotime("+".$quotation->payment_term,strtotime($quotation->exp_date)))
            <button type="button" class="btn btn-danger m-b-10">{{trans('quotation.expired')}}</button>
        @else
            @if($user->hasAccess(['invoices.write']) && $quotation->status == trans('quotation.quotation_accepted') || $orgRole=='admin' && $quotation->status == trans('quotation.quotation_accepted'))
                <a href="{{url('quotation/'.$quotation->id.'/make_invoice')}}" class="btn btn-primary m-b-10">
                    <i class="fa fa-share"></i> {{trans('quotation.invoice')}}
                </a>
            @endif
            @if($user->hasAccess(['sales_orders.write']) && $quotation->status == trans('quotation.quotation_accepted') || $orgRole=='admin' && $quotation->status == trans('quotation.quotation_accepted'))
                <a href="{{ url('quotation/'.$quotation->id.'/confirm_sales_order' ) }}" class="btn btn-primary">
                    <i class="fa fa-check"></i> {{ trans("table.confirm_sales_order") }}
                </a>
            @endif
        @endif
    </div>
    <!-- ./ notifications -->
    @include('user/'.$type.'/_form')

    @if($orgRole=='admin')
        <div class="card">
            <div class="card-header bg-white">
                <h4>{{trans('profile.history')}}</h4>
            </div>
            <div class="card-body">
                <ul class="pl-0">
                    @foreach($quotation->revisionHistory as $history )
                        <li>{{ $history->userResponsible()->first_name }} changed {{ $history->fieldName() }}
                            from {{ $history->oldValue() }} to {{ $history->newValue() }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    <div id="sendby_ajax" style="text-align:center;">
    </div>
                <!-- START MODAL SEND BY EMAIL CONTENT -->
        <div class="modal fade" id="modal-send_by_email" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <strong>{{trans('quotation.send')}} </strong>{{trans('quotation.by_email')}}
                        </h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times-circle-o"></i>
                        </button>
                    </div>
                    {!! Form::open(['url' => $type.'/send_quotation', 'method' => 'post', 'files'=> true, 'id'=>'send_quotation', 'name'=>"send_quotation"]) !!}
                    {!! Form::hidden('quotation_id', $quotation->id, ['class' => 'form-control', 'id'=>"quotation_id"]) !!}
                    <div class="modal-body">

                        <div class="form-group">
                            {!! Form::label('email_subject', trans('quotation.subject'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::text('email_subject', "Quotation (Ref ".$quotation->quotations_number.')'
                                , ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('recipients', trans('quotation.recipients'), ['class' => 'control-label']) !!}
                            <div class="controls">
                                {!! Form::select('recipients', isset($email_recipients)?$email_recipients:['',trans('quotation.recipients')], null, ['id'=>'recipients','class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="control-label"></label>
                       <textarea name="message_body" id="message_body" cols="80" rows="10" class="cke-editor form-control">
                       	<p>
                            Hello {{ $quotation->customer->full_name ?? null }}
                            ,</p>
                            <p> {{ trans('invoice.order_confirmation_from').' '. $settings['site_name'] }}: </p>
                            <p style="border-left: 1px solid #8e0000; margin-left: 30px;">
                                &nbsp;&nbsp;<strong>{{ trans('invoice.references') }}</strong><br>
                                &nbsp;&nbsp;{{ trans('invoice.order_number') }}:
                                <strong>{{ $quotation->quotations_number}}</strong><br>
                                &nbsp;&nbsp;{{ trans('invoice.order_total') }}: <strong>{{ $quotation->grand_total}}</strong><br>
                                &nbsp;&nbsp;{{ trans('invoice.order_date') }}: {{ date('m/d/Y H:i', strtotime($quotation->date))}}
                                <br>
                            </p>
                       </textarea>
                        </div>
                        <div class="form-group">
                            <label for="field-1" class="control-label">{{trans('quotation.file')}}</label>
                            <a href="" id="pdf_url" target="_blank"></a>
                            <input type="hidden" name="quotation_pdf" id="quotation_pdf" value=""
                                   class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer text-center">
                        <div id="sendby_submitbutton">
                            <button type="submit"
                                    class="btn btn-embossed btn-primary">{{trans('quotation.send')}}</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
@stop
