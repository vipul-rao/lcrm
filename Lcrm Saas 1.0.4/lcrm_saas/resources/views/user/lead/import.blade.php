
@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')

    <leads-import url="{{ url('lead') }}/"></leads-import>
@stop

{{-- Scripts --}}
@section('scripts')

@stop
