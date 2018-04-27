@extends('layouts.user')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <router-view url="{{ url('mailbox') }}"></router-view>
@stop