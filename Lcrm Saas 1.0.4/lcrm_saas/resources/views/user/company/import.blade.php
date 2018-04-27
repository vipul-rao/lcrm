
@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')

    <company-import url="{{ url('company') }}/"></company-import>
@stop

{{-- Scripts --}}
@section('scripts')

@stop
