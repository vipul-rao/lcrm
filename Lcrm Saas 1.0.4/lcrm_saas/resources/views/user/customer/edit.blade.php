@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="page-header clearfix">
    </div>
    <!-- ./ notifications -->
    @include('user/'.$type.'/_form')
    {{--@if($orgRole=='admin')--}}
        {{--<fieldset>--}}
            {{--<legend>{{trans('profile.history')}}</legend>--}}
            {{--<ul>--}}
                {{--@foreach($customer->revisionHistory as $history )--}}
                    {{--<li>{{ $history->userResponsible()->first_name }} changed {{ $history->fieldName() }}--}}
                        {{--from {{ $history->oldValue() }} to {{ $history->newValue() }}</li>--}}
                {{--@endforeach--}}
            {{--</ul>--}}
        {{--</fieldset>--}}
    {{--@endif--}}
@stop