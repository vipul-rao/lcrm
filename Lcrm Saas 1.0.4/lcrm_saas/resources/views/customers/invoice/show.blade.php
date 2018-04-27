@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-header clearfix">
                <a href="{{url('customers/invoice/'.$invoice->id.'/print_quot')}}" class="btn btn-primary m-b-10" target="">
                    <i class="fa fa-print"></i> {{trans('invoice.print')}}</a>
                @if($invoice->status==trans('invoice.open_invoice') || $invoice->status==trans('invoice.overdue_invoice'))
                    <a href="{{url('customers/payment/'.$invoice->id.'/pay')}}" class="btn btn-success m-b-10" target="">
                        <i class="fa fa-money"></i> {{trans('invoice.pay')}}</a>
                @endif
                @if(strtotime(date("m/d/Y"))>strtotime("+".$invoice->payment_term,strtotime($invoice->due_date)))
                    <button type="button" class="btn btn-danger m-b-10">{{trans('invoice.invoice_expired')}}</button>
                @endif
            </div>
            <div class="details">
                @include($type.'/_details')
            </div>
        </div>
    </div>
@stop


@section('scripts')
    <script>
        function create_pdf(invoice_id) {
            $.ajax({
                type: "GET",
                url: "{{url('customers/invoice/')}}/" + invoice_id + "/ajax_create_pdf",
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
    </script>
@endsection
