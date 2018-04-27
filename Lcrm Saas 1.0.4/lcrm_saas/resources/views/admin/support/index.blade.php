@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <router-view url="{{ url('admin/support') }}" :is-admin="true"></router-view>
@stop
