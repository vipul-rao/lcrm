
@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')

    <product-import url="{{ url('product') }}/"></product-import>
@stop

{{-- Scripts --}}
@section('scripts')

@stop
