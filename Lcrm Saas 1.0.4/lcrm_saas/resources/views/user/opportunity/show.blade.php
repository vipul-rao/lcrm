@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            @if($user->hasAccess(['quotations.write']) || $orgRole=='admin')
                <div class="page-header clearfix">
                    <a href="{{ url($type . '/'.$opportunity->id.'/convert_to_quotation/') }}"
                       class="btn btn-primary m-b-10" target="">{{trans('opportunity.convert_to_quotation')}}</a>
                </div>
            @endif
            <div class="details">
                @include('user/'.$type.'/_details')
            </div>
        </div>
    </div>
@stop
