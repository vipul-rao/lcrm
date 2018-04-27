@extends('layouts/emails')

@section('content')
<p>{{ trans('emails.hello') }} {!! $user->first_name !!} {!! $user->last_name !!},</p>

<p>{{ trans('emails.updated_your_password') }}</p>

<p><a href="{!! $forgotPasswordUrl !!}">{!! $forgotPasswordUrl !!}</a></p>

@stop
