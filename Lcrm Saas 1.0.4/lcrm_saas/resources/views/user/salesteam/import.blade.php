@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')

    <sales-team url="{{ url('salesteam') }}/"></sales-team>

@stop

{{-- Scripts --}}
@section('scripts')

@stop