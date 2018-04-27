@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
        <a href="{{ url($type . '/'.$opportunity->id.'/convert_to_quotation/') }}"
           class="btn btn-primary m-b-10" target="">{{trans('opportunity.convert_to_quotation')}}</a>
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
                    @foreach($opportunity->revisionHistory as $history )
                        <li>{{ $history->userResponsible()->first_name }} changed <strong>{{ $history->fieldName() }}</strong>
                            from {{ $history->oldValue() }} to {{ $history->newValue() }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
@stop
