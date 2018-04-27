@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="page-header clearfix">
            </div>
            <div class="details">
                @include('user/'.$type.'/_details')
            </div>
        </div>
    </div>
@stop