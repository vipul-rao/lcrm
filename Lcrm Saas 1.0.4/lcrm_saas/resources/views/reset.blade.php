@extends('layouts.frontend.user')
@section('styles')
    <link href="{{ asset('css/login_register.css') }}" rel="stylesheet" type="text/css">
@stop
@section('content')
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="m-auto col-lg-4 col-md-6 col-sm-10 col-12 signin-form">
                    <div class="box-color">
                        <h4 class="text-center">{{trans('auth.change_password')}}</h4>
                        <br>
                        {!! Form::open(['url' => url('reset_password/'.$token), 'method' => 'post']) !!}
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.email')) !!} :
                            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                            {!! Form::email('email', null, ['class' => 'form-control', 'required'=>'required']) !!}
                        </div>
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.password')) !!} :
                            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                            {!! Form::password('password', ['class' => 'form-control', 'required'=>'required']) !!}
                        </div>
                        <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.password_confirmation')) !!} :
                            <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'required'=>'required']) !!}
                        </div>
                        {!! Form::hidden('token', $token )!!}
                        <div class="form-group">
                            {!! Form::submit(trans('auth.reset'), ['class' => 'btn btn-primary btn-block']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection