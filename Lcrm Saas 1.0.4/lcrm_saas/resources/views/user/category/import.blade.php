
@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')

    <category-import url="{{ url('category') }}/"></category-import>
@stop

{{-- Scripts --}}
@section('scripts')

@stop
