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
            <a href="{{url('customers/quotation/'.$quotation->id.'/print_quot')}}" class="btn btn-primary"><i
                        class="fa fa-print"></i> {{trans('quotation.print')}}</a>
            @if(strtotime(date("m/d/Y"))>strtotime("+".$quotation->payment_term,strtotime($quotation->exp_date)))
                <button type="button" class="btn btn-danger">{{trans('quotation.expired')}}</button>
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
        function create_pdf(quotation_id) {
            $.ajax({
                type: "GET",
                url: "{{url('customers/quotation' )}}/" + quotation_id + "/ajax_create_pdf",
                data: {'_token': '{{csrf_token()}}'},
                success: function (msg) {
                    if (msg != '') {
                        $("#pdf_url").attr("href", msg)
                        var index = msg.lastIndexOf("/") + 1;
                        var filename = msg.substr(index);
                        $("#pdf_url").html(filename);
                        $("#quotation_pdf").val(filename);
                    }
                }
            });
        }
    </script>
@endsection