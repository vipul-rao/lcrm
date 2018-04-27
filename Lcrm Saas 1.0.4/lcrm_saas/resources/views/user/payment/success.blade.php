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
            <p>
                <a href="{{url('setting')}}" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left"></i> {{trans('userpayment.settings')}}
                </a>
            </p>
        </div>
    </div>
@stop
