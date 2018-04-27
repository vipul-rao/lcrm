@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <a href="#" class="btn btn-primary m-b-10" data-toggle="modal" data-target="#modal-send_by_email"
           onclick="create_pdf({{$invoice->id}})"><i class="fa fa-envelope-o"></i> {{trans('invoice.email_send')}}</a>
        <a href="{{url('invoice/'.$invoice->id.'/print_quot')}}" class="btn btn-primary m-b-10" target=""><i
                    class="fa fa-print"></i> {{trans('invoice.print')}}</a>
        @if(strtotime(date("m/d/Y"))>strtotime("+".$invoice->payment_term,strtotime($invoice->due_date)))
            <button type="button" class="btn btn-danger m-b-10">{{trans('invoice.invoice_expired')}}</button>
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="details">
                @include('user/'.$type.'/_details')
            </div>
        </div>
    </div>
    <!-- START MODAL SEND BY EMAIL CONTENT -->
    <div class="modal fade" id="modal-send_by_email" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><strong>{{trans('invoice.email_send')}}</strong></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times-circle-o"></i>
                    </button>
                </div>
                <div id="sendby_ajax" style="text-align:center;"></div>
                {!! Form::open(['url' => $type, 'method' => 'post', 'files'=> true, 'id'=>'send_invoice', 'name'=>"send_invoice"]) !!}
                {!! Form::hidden('invoice_id', $invoice->id, ['class' => 'form-control', 'id'=>"invoice_id"]) !!}
                <div class="modal-body">

                    <div class="form-group">
                        {!! Form::label('email_subject', trans('invoice.subject'), ['class' => 'control-label']) !!}
                        <div class="controls">
                            {!! Form::text('email_subject', trans('invoice.demo_company_invoice')." (Ref ".$invoice->invoice_number.')'
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
                       <textarea name="message_body" id="message_body" cols="80" rows="10" class="cke-editor resize_vertical form-control">
                       	<p>
                            Hello {{(isset($invoice->customer)?$invoice->customer->full_name:"")}}
                            ,</p>
                            <p> {{ trans('invoice.order_confirmation_from').' '. $settings['site_name'] }}: </p>
                            <p style="border-left: 1px solid #8e0000; margin-left: 30px;">
                                &nbsp;&nbsp;<strong>{{ trans('invoice.references') }}</strong><br>
                                &nbsp;&nbsp;{{ trans('invoice.order_number') }}:
                                <strong>{{ $invoice->invoice_number}}</strong><br>
                                &nbsp;&nbsp;{{ trans('invoice.order_total') }}: <strong>{{ $invoice->grand_total}}</strong><br>
                                &nbsp;&nbsp;{{ trans('invoice.order_date') }}: {{ date('m/d/Y H:i', strtotime($invoice->date))}}
                                <br>
                            </p>
                       </textarea>
                    </div>
                    <div class="form-group">
                        <label for="field-1" class="control-label">{{trans('invoice.file')}}</label>
                        <a href="" id="pdf_url" target="_blank"></a>
                        <input type="hidden" name="invoice_pdf" id="invoice_pdf" value=""
                               class="form-control">
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <div id="sendby_submitbutton">
                        <button type="submit"
                                class="btn btn-primary">{{trans('invoice.send')}}</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script>
        $("#recipients").select2({
            placeholder:"{{ trans('quotation.recipients') }}",
            theme: 'bootstrap'
        });
        function create_pdf(invoice_id) {
            $.ajax({
                type: "GET",
                url: "{{url('invoice' )}}/" + invoice_id + "/ajax_create_pdf",
                data: {'_token': '{{csrf_token()}}'},
                success: function (msg) {
                    if (msg != '') {
                        $("#pdf_url").attr("href", msg);
                        var index = msg.lastIndexOf("/") + 1;
                        var filename = msg.substr(index);
                        $("#pdf_url").html(filename);
                        $("#invoice_pdf").val(filename);
                    }
                }
            });
        }

        $("#send_invoice").submit(function (e) {
            e.preventDefault();
            $.post( "{{url('invoice/send_invoice')}}",
                $('#send_invoice').serialize()
            )
                .done(function( msg ) {
                    $('body,html').animate({scrollTop: 0}, 200);
                    $("#sendby_ajax").html(msg);
                    setTimeout(function(){
                        $("#sendby_ajax").hide();
                    },5000);
                    $("#modal-send_by_email").modal('hide');
                });
        });
        $("#modal-send_by_email").on('hide.bs.modal', function () {
            $("#recipients").find("option").attr('selected',false);
            $("#recipients").select2({
                placeholder:"{{ trans('invoice.recipients') }}",
                theme: 'bootstrap'
            });
        });
    </script>
@stop