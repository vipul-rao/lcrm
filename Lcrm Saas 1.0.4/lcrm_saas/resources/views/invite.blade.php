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
                        <h4 class="text-center text-white">{{trans('auth.create_account')}}</h4>
                        <br>
                        {!! Form::open(['url' => url('invite/'.$inviteUser->code), 'method' => 'post']) !!}

                        <div class="form-group has-feedback {{ $errors->has('first_name') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.first_name')) !!} :
                            {!! Form::text('first_name', null, ['class' => 'form-control', 'required'=>'required']) !!}
                            <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('last_name') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.last_name')) !!} :
                            {!! Form::text('last_name', null, ['class' => 'form-control', 'required'=>'required']) !!}
                            <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
                        </div>
                        <div class="form-group has-feedback">
                            {!! Form::label(trans('auth.email')) !!} :
                            {!! $inviteUser->email !!}
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.password')) !!} :
                            {!! Form::password('password', ['class' => 'form-control', 'required'=>'required']) !!}
                            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                            {!! Form::label(trans('auth.password_confirmation')) !!} :
                            {!! Form::password('password_confirmation', ['class' => 'form-control', 'required'=>'required']) !!}
                            <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                            {!! Form::label(trans('staff.phone_number')) !!} :
                            {!! Form::text('phone_number', null, ['class' => 'form-control', 'required'=>'required']) !!}
                            <span class="help-block">{{ $errors->first('phone_number', ':message') }}</span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{trans('auth.register')}}</button>
                        {!! Form::close() !!}
                    </div>
                    <h5 class="text-center text-default"><a href="{{url('signin')}}" class="text-primary _600">{{trans('auth.login')}}?</a>
                    </h5>
                </div>
            </div>
        </div>
    </div>
@stop
