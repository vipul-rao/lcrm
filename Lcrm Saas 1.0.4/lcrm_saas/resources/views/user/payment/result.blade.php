@extends('layouts.subscription')

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
            <p>{{ $paypalResponse }}</p>
            <p>
                <a href="{{url('dashboard')}}" class="btn btn-fw btn-sm primary"><i class="fa fa-arrow-left"></i> {{trans('payment.home')}} </a>
            </p>
        </div>
    </div>
@stop

{{-- page level scripts --}}
@section('footer_scripts')
@stop
