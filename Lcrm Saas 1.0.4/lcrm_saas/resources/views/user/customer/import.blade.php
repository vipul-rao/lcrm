@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')

    <customer-import url="{{ url('customer') }}/"></customer-import>

@stop

{{-- Scripts --}}
@section('scripts')

@stop