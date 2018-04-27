@extends('layouts/emails')

@section('content')
New message from <b>{{$user}}</b>:<br>
{{$bodyMessage}}
@stop
